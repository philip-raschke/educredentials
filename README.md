## Moodle 4.1.2:

- Moodle 4.1.2 is a popular E-Learning platform used for creating and managing online courses.
- It is an open-source software developed by a community, offering a range of features for learners and educators.

### Stakeholders:

- **Learners**: They are the primary users of Moodle, accessing course content, completing assignments, participating in discussions, and tracking their progress.
- **Educators**: They utilize Moodle to create courses, provide course materials, assign tasks, conduct tests, and monitor learners' progress.
- **Administrators**: Responsible for installing, configuring, and maintaining the Moodle platform. They manage user accounts, ensure smooth functioning, and ensure system security and availability.
- **Developers**: The Moodle community comprises developers who continuously work on improving the platform, adding new features, fixing bugs, and developing extensions.

### Badges:

- Badges are a prominent feature of Moodle 4.1.2.
- They are virtual awards or badges that can be granted to learners for their achievements and progress in a course.
- Badges can be awarded when learners meet specific goals or criteria, such as completing a course, reaching a certain score, or actively engaging in discussions.
- The granting of badges is based on criteria set by course instructors or administrators.
- Learners receive badges automatically in their profiles when they fulfill the specified criteria.

## WampServer64:

- WampServer64 is a web development environment commonly used to set up local web servers on Windows operating systems.
- It allows running web applications like Moodle on a local computer for testing and development purposes before deploying them to a production server.
- WampServer64 can be utilized to install and operate Moodle locally, enabling developers and administrators to test the Moodle installation, make customizations, and validate changes before deploying them to a public or production server.

# Moodle Local Setup

**Download and Install WampServer64:**

1. Visit the official WampServer website at [https://www.wampserver.com/en/download-wampserver-64bits/](https://www.wampserver.com/en/download-wampserver-64bits/).
2. Choose the appropriate version for your operating system and download WampServer64.
3. After the download completes, extract the contents of the ZIP archive to your local hard drive (default installation path: "C:\wamp64").

**Configure WampServer64:**

1. Open the extracted WampServer64 directory and locate the "C:\wamp64\www" directory.
2. Create a file named ".htaccess" within the "C:\wamp64\www" directory.
3. Open the ".htaccess" file with a text editor and add the line "php_value max_input_vars 5000".
   This configuration option optimizes the handling of input variables in the plugin, ensuring smooth capture and processing of learner data.

**Download and Install Moodle 4.1.2:**

1. Visit the official Moodle website at [https://moodle.en.uptodown.com/windows/download](https://moodle.en.uptodown.com/windows/download).
2. From there, download the appropriate Windows version of Moodle 4.1.2.
3. Unzip the Moodle ZIP folder to "C:\wamp64\www".
4. Rename your Moodle folder to "moodle4" ("C:\wamp64\www\moodle4").

**Start Moodle 4.1.2 in Browser**

1. Open your preferred browser and enter "http://localhost/moodle4/" in the address bar.
2. Follow the installation steps displayed in the browser.
3. Once the installation is complete, you will be logged in as the Administrator.

## Issuing Badges as Teacher

1. To create a new course, click on "My courses" at the top of the page ("http://localhost/moodle4/my/courses.php").
2. Click on "New course" to create a new course ("http://localhost/moodle4/course/edit.php?category=1").
3. Customize the course according to your preferences.
4. Invite participants to join the course.
5. Once the invitations are accepted, course instructors can issue badges.
6. Click on "More" on the course page.
7. Select "Badges" from the dropdown menu.
8. Click on "Add a new badge".
9. Create the badge according to your preferences, including criteria, badge picture, etc.
10. Students who meet the badge requirements you set will receive the corresponding badge.

In the `educredentials/documents` directory of this GitHub repository, you will find transcripts and project materials such as diagrams and research.

The `educredentials/badge_data` in this GitHub repository is the Moodle plugin for verifiable credentials. Inside this directory, you will find a README file explaining how to install and use the plugin.

