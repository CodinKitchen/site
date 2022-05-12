<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Enum\UserRole;
use App\Entity\Meeting;
use Symfony\Component\Filesystem\Path;
use App\Service\Meeting\MeetingService;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsCommand(
    name: 'app:bbb:migrate-users',
    description: 'Add a short description for your command',
    hidden: true,
)]
class BbbMigrateUsersCommand extends Command
{
    public function __construct(
        private MeetingService $meetingService,
        private SerializerInterface $serializer,
        private EntityManagerInterface $entityManager,
        private string $rootDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'CSV file path to load');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $csvPath */
        $csvPath = $input->getArgument('path');
        $csvPath = Path::makeAbsolute($csvPath, $this->rootDir);
        /** @var string[][] $users */
        $users = $this->serializer->deserialize(file_get_contents($csvPath), 'string[][]', CsvEncoder::FORMAT);

        foreach ($users as $user) {
            $userEntity = new User();
            $userEntity->setFirstname(explode(' ', $user['name'])[0]);
            $userEntity->setEmail($user['email']);
            $userEntity->setBbbMeetingId($user['bbb_id']);
            $userEntity->addRole(UserRole::ROLE_ATTENDEE);
            $this->entityManager->persist($userEntity);

            $recordings = $this->meetingService->getRecordings($userEntity);
            foreach ($recordings as $recording) {
                $meeting = new Meeting();
                $meeting->setAttendee($userEntity);
                $meeting->setStatus(Meeting::STATUS_PLAYABLE);
                $startTime = round((float) $recording->getStartTime() / 1000);
                $startTime = DateTimeImmutable::createFromFormat('U', (string) $startTime);
                if (!$startTime) {
                    throw new InvalidArgumentException('Recording timestamp could not be converted to date');
                }
                $startTime = $startTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
                $meeting->setTimeSlot($startTime);
                $meeting->setBbbRecordingId($recording->getRecordId());
                $meeting->setDuration((int) round((int) $recording->getPlaybackLength() / 60));
                $this->entityManager->persist($meeting);
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf('Successfully imported %d users', count($users)));

        return Command::SUCCESS;
    }
}
