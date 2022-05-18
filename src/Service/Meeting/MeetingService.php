<?php

namespace App\Service\Meeting;

use App\Entity\Meeting;
use App\Entity\User;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Core\MeetingLayout;
use BigBlueButton\Core\Record;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use BigBlueButton\Responses\CreateMeetingResponse;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MeetingService
{
    public function __construct(
        private BigBlueButton $bigBlueButton,
        private Packages $packages,
        private UrlHelper $urlHelper,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function join(Meeting $meeting, bool $moderator = false): string
    {
        $meetingId = (string) $meeting->getAttendee()->getBbbMeetingId();
        $logoUrl = $this->urlHelper->getAbsoluteUrl($this->packages->getUrl('build/images/logo_codinkitchen.png'));
        $logoutUrl = $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $createMeetingParams = new CreateMeetingParameters($meetingId, sprintf('Meeting %d', $meeting->getId()));
        $createMeetingParams->setAllowModsToEjectCameras(true);
        $createMeetingParams->setBreakoutRoomsEnabled(false);
        $createMeetingParams->setBreakoutRoomsRecord(false);
        $createMeetingParams->setBreakoutRoomsPrivateChatEnabled(false);
        $createMeetingParams->setMeetingLayout(MeetingLayout::SMART_LAYOUT);
        $createMeetingParams->setRecord(true);
        $createMeetingParams->setAllowStartStopRecording(true);
        $createMeetingParams->setLogoutUrl($logoutUrl);
        $createMeetingParams->setLogo($logoUrl);
        $createMeetingParams->setEndCallbackUrl($this->urlGenerator->generate('meeting_ended', ['id' => $meeting->getId()]));
        // $createMeetingParams->setRecordingReadyCallbackUrl($this->urlGenerator->generate('meeting_replay_ready', ['id' => $meeting->getId()]));
        $createMeetingParams->setRecordingReadyCallbackUrl('https://webhook.site/1071c79b-9570-4bdb-84d9-2a9293c7492c');
        $meetingResponse = $this->bigBlueButton->createMeeting($createMeetingParams);

        $password = $moderator ? $meetingResponse->getModeratorPassword() : $meetingResponse->getAttendeePassword();
        $firstname = $moderator ? 'Gauthier' : $meeting->getAttendee()->getFirstname();

        if ($meetingResponse->getReturnCode() === CreateMeetingResponse::FAILED) {
            $meetingInfoParams = new GetMeetingInfoParameters($meetingId, '');
            $meetingResponse = $this->bigBlueButton->getMeetingInfo($meetingInfoParams);
            $password = (string) ($moderator ? $meetingResponse->getMeeting()->getModeratorPassword() : $meetingResponse->getMeeting()->getAttendeePassword());
        }

        $joinMeetingParams = new JoinMeetingParameters($meetingId, $firstname, $password);
        $joinMeetingParams->setRedirect(true);

        return $this->bigBlueButton->getJoinMeetingURL($joinMeetingParams);
    }

    /**
     * @return Record[]
     */
    public function getRecordings(User $user): array
    {
        $recordingsParams = new GetRecordingsParameters();
        $recordingsParams->setMeetingId((string) $user->getBbbMeetingId());
        return $this->bigBlueButton->getRecordings($recordingsParams)->getRecords();
    }

    public function getPlaybackUrl(Meeting $meeting): string
    {
        $recordingsParams = new GetRecordingsParameters();
        $recordingsParams->setRecordId($meeting->getBbbRecordingId());
        return $this->bigBlueButton->getRecordings($recordingsParams)->getRecords()[0]->getPlaybackUrl();
    }
}
