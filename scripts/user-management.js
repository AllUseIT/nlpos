$(document).ready(function() {
    loadUsers();

    $("#createUserBtn").click(function() {
        $("#createUserPopup").show();
    });

    $("#user-create-close-btn").click(function() {
        $("#createUserPopup").hide();
    });

    $("#user-edit-close-btn").click(function() {
        $("#editUserPopup").hide();
    });

    $("#create-user-button").click(function(e) {
        e.preventDefault();
        
        let username = $("input[name='username']").val();
        let name = $("input[name='name']").val();
        let surname = $("input[name='surname']").val();
        let password = $("input[name='password']").val();
        let job = $("select[name='job']").val();
        let branch = $("select[name='branch']").val();
        let active = $("select[name='active']").val();
        let till = $("select[name='till']").val(); // Use select dropdown value
    
        $.ajax({
            url: "./API/insert_user.php",
            type: "POST",
            data: {
                username: username,
                name: name,
                surname: surname,
                password: password,
                job: job,
                branch: branch,
                active: active,
                till: till // Include the new field
            },
            success: function(response) {
                alert(response);
                $("#createUserPopup").hide();
                loadUsers(); // Reload the table
            },
            error: function(error) {
                alert("Error: " + error);
            }
        });
    });

    $(document).on("click", ".edit-button", function() {
        let id = $(this).closest("tr").find(".id").text(); // Get ID from table row
        let username = $(this).closest("tr").find(".username").text();
        let name = $(this).closest("tr").find(".name").text();
        let surname = $(this).closest("tr").find(".surname").text();
        let password = $(this).closest("tr").find(".password").text();
        let job = $(this).closest("tr").find(".job").text();
        let branch = $(this).closest("tr").find(".branch").text();
        let active = $(this).closest("tr").find(".active").text();
        let till = $(this).closest("tr").find(".till").text(); // Get till value

        $("#editUserPopup").show();

        // Fill the edit form fields with the user data
        $("#editUserForm input[name='id']").val(id); // Populate ID
        $("#editUserForm input[name='username']").val(username);
        $("#editUserForm input[name='name']").val(name);
        $("#editUserForm input[name='surname']").val(surname);
        $("#editUserForm input[name='password']").val(password);
        $("#editUserForm select[name='job']").val(job);
        $("#editUserForm select[name='branch']").val(branch);
        $("#editUserForm select[name='active']").val(active);
        $("#editUserForm select[name='till']").val(till); // Set till value
    });

    $("#editUserForm").submit(function(e){
        e.preventDefault();
        
        let id = parseInt($("#editUserForm input[name='id']").val()); // Ensure ID is an integer
        let newUsername = $("#editUserForm input[name='username']").val();
        let newName = $("#editUserForm input[name='name']").val();
        let newSurname = $("#editUserForm input[name='surname']").val();
        let newPassword = $("#editUserForm input[name='password']").val();
        let newJob = $("#editUserForm select[name='job']").val(); // Retrieve the job value
        let newBranch = $("#editUserForm select[name='branch']").val();
        let newActive = $("#editUserForm select[name='active']").val();
        let newTill = $("#editUserForm select[name='till']").val(); // Use select for till
    
        console.log("Edit Form Data:", {
            id: id,
            username: newUsername,
            name: newName,
            surname: newSurname,
            password: newPassword,
            job: newJob, // Include job in the data object
            branch: newBranch,
            active: newActive,
            till: newTill // Include the new field
        });
    
        // AJAX request to update the user
        $.ajax({
            url: "./API/update_user.php",
            type: "POST",
            data: {
                id: id,
                username: newUsername,
                name: newName,
                surname: newSurname,
                password: newPassword,
                job: newJob, // Include job in the data sent to the server
                branch: newBranch,
                active: newActive,
                till: newTill // Include the new field
            },
            success: function(response){
                console.log("Update Response:", response);
                alert(response); // Show the response message
                $("#editUserPopup").hide();
                loadUsers(); // Reload the table
            },
            error: function(error){
                console.log("Update Error:", error);
                alert("Error: " + error);
            }
        });
    });
    

    $(document).on("click", "#delete-user-button", function() {
        let id = $("#editUserForm input[name='id']").val(); // Get the user ID from the form
        if (confirm("Are you sure you want to delete this user?")) {
            deleteUser(id); // Call the function to delete the user
        }
    });
    
    function deleteUser(id) {
        $.ajax({
            url: "./API/delete_user.php",
            type: "POST",
            data: { id: id },
            success: function(response) {
                alert(response); // Display the response message
                $("#editUserPopup").hide();
                loadUsers(); // Reload the table
            },
            error: function(error) {
                console.log("Delete Error:", error);
                alert("Error: " + error);
            }
        });
    }
    

    // Function to load users
    function loadUsers() {
        $.ajax({
            url: "./API/get_users.php",
            type: "GET",
            success: function(response) {
                let users = JSON.parse(response);
                let tbody = $("#product-table tbody");
                tbody.empty(); // Clear existing rows
                
                users.forEach(function(user) {
                    let row = `
                        <tr>
                            <td class="id" style="display: none;">${user.id}</td> <!-- Include ID column -->
                            <td class="username">${user.username}</td>
                            <td class="name">${user.name}</td>
                            <td class="surname">${user.surname}</td>
                            <td class="password">${user.password}</td>
                            <td class="job">${user.job}</td>
                            <td class="till">${user.till}</td> <!-- Add till column -->
                            <td class="branch">${user.branch}</td>
                            <td class="active">${user.active}</td>
                            <td><button class="edit-button">Edit</button></td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            },
            error: function(error) {
                alert("Error: " + error);
            }
        });
    }
});
