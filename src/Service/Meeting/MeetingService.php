<?php

namespace App\Service\Meeting;

use App\Entity\Meeting;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Core\MeetingLayout;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use BigBlueButton\Responses\CreateMeetingResponse;
use Symfony\Component\Security\Core\Security;

class MeetingService
{
    public function __construct(private BigBlueButton $bigBlueButton, private Security $security)
    {
    }

    public function join(Meeting $meeting, bool $moderator = false): string
    {
        $createMeetingParams = new CreateMeetingParameters((string) $meeting->getAttendee()->getBbbMeetingId(), sprintf('Meeting %d', $meeting->getId()));
        $createMeetingParams->setAllowModsToEjectCameras(true);
        $createMeetingParams->setBreakoutRoomsEnabled(false);
        $createMeetingParams->setBreakoutRoomsRecord(false);
        $createMeetingParams->setBreakoutRoomsPrivateChatEnabled(false);
        $createMeetingParams->setMeetingLayout(MeetingLayout::SMART_LAYOUT);
        $meetingResponse = $this->bigBlueButton->createMeeting($createMeetingParams);

        $password = $moderator ? $meetingResponse->getModeratorPassword() : $meetingResponse->getAttendeePassword();
        $firstname = $moderator ? 'Gauthier' : $meeting->getAttendee()->getFirstname();

        if ($meetingResponse->getReturnCode() === CreateMeetingResponse::FAILED) {
            $meetingInfoParams = new GetMeetingInfoParameters((string) $meeting->getAttendee()->getBbbMeetingId(), '');
            $meetingResponse = $this->bigBlueButton->getMeetingInfo($meetingInfoParams);
            $password = (string) ($moderator ? $meetingResponse->getRawXml()->moderatorPW : $meetingResponse->getRawXml()->attendeePW);
        }

        $joinMeetingParams = new JoinMeetingParameters((string) $meeting->getAttendee()->getBbbMeetingId(), $firstname, $password);
        $joinMeetingParams->setRedirect(true);

        return $this->bigBlueButton->getJoinMeetingURL($joinMeetingParams);
    }
}
