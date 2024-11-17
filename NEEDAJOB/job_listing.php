<?php
// Fetch job listings from the database
$sql = "SELECT * FROM jobs";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($job = $result->fetch_assoc()) {
        echo '<div class="job-card">';
        echo '<h4>' . $job['titles'] . '</h4>';
        echo '<p>' . $job['description'] . '</p>';
        echo '<p>Location: ' . $job['location'] . '</p>';
        echo '<p>Salary: ' . $job['salary'] . '</p>';
        echo '<a href="apply_job.php?job_id=' . $job['ID'] . '">Apply Now</a>'; // Apply link
        echo '</div>';
    }
}
?>
