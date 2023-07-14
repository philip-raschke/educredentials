# Moodle Plugin for Verifiable Credentials

The contribution of this repository is the development of a Moodle plugin that enables educational institutions to export verifiable credentials. These credentials serve the purpose of validating language certificates. The plugin utilizes Moodle and adheres to the Open Badges standard, issuing verifiable credentials based on the W3C Verifiable Credentials Data Model. By aligning with the principles of Self-Sovereign Identity (SSI), the plugin contributes to the advancement of the IDunion network. Within this network, it facilitates the issuance, storage, and verification of these verifiable credentials. Our contribution focuses specifically on the issuance of these credentials.
## Moodle

Moodle is an open-source e-learning platform used for creating and managing online courses. It offers a range of features for learners and educators [1].

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

## References
- [1] Beatty, B., & Ulasewicz, C. (2006). Faculty perspectives on moving from Blackboard to the Moodle learning management system. TechTrends, 50(4), 36-45.
