<div class = "profileCell">
    <table>
        <tr>
<!--
            $("#profilePicture").attr("alt", "Image not found");
    $("#profilePicture").attr("onError","this.onerror=null;this.src='files/profilePictures/0.png';");

    $("#profilePicture").attr("src","files/profilePictures/" + id +".png");
-->

            <td width = "60px" rowspan="1" height="60px" class = "profile_picture">
                <img src = "files/profilePictures/<?php echo $e['applicantProfileID']; ?>.png" alt="=Image not found" onerror="this.onerror=null;this.src='files/profilePictures/0.png';" width="100%">
            </td>
            <td rowspan="2" id = "profile_info">
                <span id = "profile_name"><?php echo $e['lastName']; ?></span>
                <span id = "profile_pastTitle"><?php echo $e['firstName']; ?></span>
                <span id = "profiel_yearsOfExperience"><?php echo $e['middleInitial']; ?></span>
                <span id = "profile_desiredPosition"><?php echo $e['positionAppliedFor']; ?></span>
                <span id = "profile_bio"><?php echo $e['overview']; ?></span>
            </td>
            <td width = "30px" rowspan="2" id = "profile_options_container">
                <div id = "profile_options">
                    <div class = "activeFolder">Folder Name</div>
                    <ul>
                        <li>Test</li>
                        <li>Folder name</li>
                        <li>Yeah</li>
                    </ul>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr height="auto">
            <td colspan="3">
                <div id = "profile_bottomBar">
                    <div class = "skillsBar">
                        <div>C++</div>
                        <div>Objective C</div>
                        <div>Leaderretsdfaship</div>
                        <div>Pasdfnctuality</div>
                    </div>
                    <div class = "rateLabel">
                    P 30,000
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
<div class = "customHr"></div>