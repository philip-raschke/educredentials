# Moodle Plugin for Verifiable Credentials

The development of a Moodle plugin for exporting verifiable credentials in educational institutions addresses the growing need for validation and verification of educational achievements in the digital learning era. This plugin leverages Moodle and the Open Badges standard to issue verifiable credentials based on the W3C Verifiable Credentials Data Model. It specifically integrates with the IDunion network, which is built on Self-Sovereign Identity (SSI) principles, allowing for the issuance and storage of verifiable credentials within the network. These credentials validate language course completion and language proficiency certificates while benefiting from the decentralized and privacy-enhancing features of SSI.

## Moodle

Moodle is a popular open-source e-learning platform used for creating and managing online courses. It offers a range of features for learners and educators.

## Stakeholders

- **Learners**: Primary users who access course content, complete assignments, participate in discussions, and track their progress.
- **Educators**: Utilize Moodle to create courses, provide materials, assign tasks, conduct tests, and monitor learners' progress.
- **Administrators**: Responsible for installing, configuring, and maintaining the Moodle platform, managing user accounts, and ensuring system security and availability.
- **Developers**: The Moodle community includes developers who continuously improve the platform, add features, fix bugs, and develop extensions.

## Badges

In Moodle, badges are verifiable credentials awarded to learners for their achievements and progress in a course. Badges can be granted based on specific goals or criteria, such as course completion, reaching a target score, or active participation in discussions. Course instructors or administrators set the criteria, and learners receive badges automatically in their profiles when they meet the requirements.

## Moodle Local Setup

### Prerequisites: WampServer64

1. Visit the official WampServer website at [https://www.wampserver.com/en/download-wampserver-64bits/](https://www.wampserver.com/en/download-wampserver-64bits/) and download WampServer64 for your operating system.
2. Extract the downloaded ZIP archive to your local hard drive (default installation path: "C:\wamp64").

### Installation Steps

1. Download the appropriate Windows version of Moodle 4.1.2 from the official Moodle website at [https://moodle.en.uptodown.com/windows/download](https://moodle.en.uptodown.com/windows/download).
2. Extract the Moodle ZIP folder to "C:\wamp64\www" and rename it to "moodle4".
3. Open your preferred browser and enter "http://localhost/moodle4/" in the address bar.
4. Follow the installation steps displayed in the browser to complete the installation. You will be logged in as the Administrator upon completion.

## Issuing Badges as a Teacher

1. Click on "My courses" at the top of the Moodle page ("http://localhost/moodle4/my/courses.php") to create a new course.
2. Click on "New course" to create a course ("http://localhost/moodle4/course/edit.php?category=1").
3. Customize the course according to your preferences.
4. Invite participants to join the course.
5. Once participants accept the invitations, course instructors can issue badges.
6. On the course page, click on "More" and select "Badges" from the dropdown menu.
7. Click on "Add a new badge" to create a badge.
8. Customize the badge according to your preferences, including criteria and badge picture.
9. Students who meet the set requirements will receive the corresponding badge.

## Repository Contents

- `educredentials/documents`: Contains transcripts, project materials, diagrams, and research related to the verifiable credentials plugin.
- `educredentials/badge_data`: Contains the Moodle plugin for verifiable credentials. Inside this directory, you will find a README file with installation and usage instructions.

For any further information or inquiries, please contact us at krause@tu-berlin.de.
