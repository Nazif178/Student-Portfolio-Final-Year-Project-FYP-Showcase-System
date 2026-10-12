
<?php
session_start();
require "db.php";
require "functions.php";
require_role("Student");

$pageTitle = "Student Dashboard";
include "header.php";
?>
<h1>Student Dashboard</h1>
<p>Search for assignments. Results update as you type.</p>
<div class="content-card">
    <h3>Quick Links</h3>
    <a class="button-link" href="submit_assignment.php">Submit Assignment</a>
    <a class="button-link" href="my_submissions.php">View My Submissions</a>
</div>
<div class="content-card">
    <label for="search">Search assignments</label>
    <input type="search" id="search" placeholder="Type an assignment title or keyword..." autocomplete="off">
    <div id="assignmentList" aria-live="polite"><p>Loading assignments...</p></div>
</div>
<script>
const searchBox = document.getElementById("search");
const list = document.getElementById("assignmentList");

async function loadAssignments() {
    list.textContent = "Searching...";

    try {
        const response = await fetch(
            "search_assignments.php?q=" + encodeURIComponent(searchBox.value),
            { headers: { "Accept": "application/json" } }
        );

        if (!response.ok) {
            throw new Error("Search request failed (" + response.status + ").");
        }

        const assignments = await response.json();
        list.replaceChildren();

        if (assignments.length === 0) {
            list.textContent = "No assignments found.";
            return;
        }

        assignments.forEach(function(item) {
            const card = document.createElement("div");
            card.className = "assignment-card";

            const title = document.createElement("h4");
            title.textContent = item.title;

            const description = document.createElement("p");
            description.textContent = item.description;

            const link = document.createElement("a");
            link.className = "button-link";
            link.textContent = "Submit Assignment";
            link.href = "submit_assignment.php?id=" + encodeURIComponent(item.assignment_id);

            card.append(title, description, link);
            list.appendChild(card);
        });
    } catch (error) {
        list.textContent = "Could not load assignments. Check that you are logged in as a Student and that search_assignments.php works.";
        console.error(error);
    }
}

searchBox.addEventListener("input", loadAssignments);
loadAssignments();
</script>
<?php include "footer.php"; ?>
