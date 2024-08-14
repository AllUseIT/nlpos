<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="./styles/users.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            loadUsers();

            $("#createUserBtn").click(function() {
                $("#createUserPopup").fadeIn();
            });

            $(".popup-close").click(function() {
                $(this).closest(".popup").fadeOut();
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
                        active: active
                    },
                    success: function(response) {
                        alert(response);
                        $("#createUserPopup").fadeOut();
                        loadUsers(); // Reload the table
                    },
                    error: function(error) {
                        alert("Error: " + error);
                    }
                });
            });

            $(document).on("click", ".edit-button", function() {
                let id = $(this).closest("tr").find(".id").text();
                let username = $(this).closest("tr").find(".username").text();
                let name = $(this).closest("tr").find(".name").text();
                let surname = $(this).closest("tr").find(".surname").text();
                let password = $(this).closest("tr").find(".password").text();
                let job = $(this).closest("tr").find(".job").text();
                let branch = $(this).closest("tr").find(".branch").text();
                let active = $(this).closest("tr").find(".active").text();

                $("#editUserPopup").fadeIn();

                $("#editUserForm input[name='id']").val(id);
                $("#editUserForm input[name='username']").val(username);
                $("#editUserForm input[name='name']").val(name);
                $("#editUserForm input[name='surname']").val(surname);
                $("#editUserForm input[name='password']").val(password);
                $("#editUserForm select[name='job']").val(job);
                $("#editUserForm select[name='branch']").val(branch);
                $("#editUserForm select[name='active']").val(active);
            });

            $("#editUserForm").submit(function(e) {
                e.preventDefault();
                
                let id = parseInt($("#editUserForm input[name='id']").val());
                let newUsername = $("#editUserForm input[name='username']").val();
                let newName = $("#editUserForm input[name='name']").val();
                let newSurname = $("#editUserForm input[name='surname']").val();
                let newPassword = $("#editUserForm input[name='password']").val();
                let newJob = $("#editUserForm select[name='job']").val();
                let newBranch = $("#editUserForm select[name='branch']").val();
                let newActive = $("#editUserForm select[name='active']").val();

                $.ajax({
                    url: "./API/update_user.php",
                    type: "POST",
                    data: {
                        id: id,
                        username: newUsername,
                        name: newName,
                        surname: newSurname,
                        password: newPassword,
                        job: newJob,
                        branch: newBranch,
                        active: newActive
                    },
                    success: function(response) {
                        alert(response);
                        $("#editUserPopup").fadeOut();
                        loadUsers(); // Reload the table
                    },
                    error: function(error) {
                        alert("Error: " + error);
                    }
                });
            });

            $(document).on("click", "#delete-user-button", function() {
                let id = $("#editUserForm input[name='id']").val();
                if (confirm("Are you sure you want to delete this user?")) {
                    deleteUser(id);
                }
            });

            function deleteUser(id) {
                $.ajax({
                    url: "./API/delete_user.php",
                    type: "POST",
                    data: { id: id },
                    success: function(response) {
                        alert(response);
                        $("#editUserPopup").fadeOut();
                        loadUsers(); // Reload the table
                    },
                    error: function(error) {
                        alert("Error: " + error);
                    }
                });
            }

            function loadUsers() {
                $.ajax({
                    url: "./API/get_users.php",
                    type: "GET",
                    success: function(response) {
                        let users = JSON.parse(response);
                        let tbody = $("#product-table tbody");
                        tbody.empty();
                        
                        users.forEach(function(user) {
                            let row = `
                                <tr>
                                    <td class="id" style="display: none;">${user.id}</td>
                                    <td class="username">${user.username}</td>
                                    <td class="name">${user.name}</td>
                                    <td class="surname">${user.surname}</td>
                                    <td class="job">${user.job}</td>
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
    </script>
</head>
<body>
    <div class="main-users-panel">
        <div>
            <h2 class="login-h2">Users</h2>
        </div>
        <div class="main-create-user-button">
            <button class="create-user-button" id="createUserBtn">Create New User</button>
        </div>
        <div class="main-user-top">
            <div class="table-container">
                <table class="main-table" id="product-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Surname</th>
                            <th>Job</th>
                            <th>Branch</th>
                            <th>Active</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
        <div class="main-users-bottom">
            <div class="main-user-bottom-left">
                <div>
                    <h2 class="login-h2">Non Active Users</h2>
                </div>
                <div class="main-active-user-table">
                    <table class="active-user-table" id="active-user-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Surname</th>
                                <th>Branch</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php include './API/non_active_users.php'; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="main-user-bottom-right">
                <div>
                    <h2 class="login-h2">Admin Users</h2>
                </div>
                <div class="main-admin-user-table">
                    <table class="admin-user-table" id="admin-user-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Surname</th>
                                <th>Branch</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php include './API/admin_users.php'; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="popup" id="createUserPopup">
        <div style="display: flex;justify-content: center;align-items: center;height: -webkit-fill-available;">
            <div class="popup-content">
                <div class="popup-header">
                    <h2>Create New User</h2>
                    <span class="popup-close">×</span>
                </div>
                <div class="popup-body">
                    <form id="createUserForm">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="surname">Surname</label>
                            <input type="text" id="surname" name="surname" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="job">Job</label>
                            <select id="job" name="job" required>
                                <option>User</option>
                                <option>Supervisor</option>
                                <option>Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="branch">Branch</label>
                            <select id="branch" name="branch" required>
                                <option value="montana">Montana</option>
                                <option value="zambezi">Zambezi</option>
                                <option value="menlyn">Menlyn</option>
                                <option value="centurion">Centurion</option>
                                <option value="daspoort">Daspoort</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="active">Active</label>
                            <select id="active" name="active" required>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" id="create-user-button">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="popup" id="editUserPopup">
        <div style="display: flex;justify-content: center;align-items: center;height: -webkit-fill-available;">
            <div class="popup-content">
                <div class="popup-header">
                    <h2>Edit User</h2>
                    <span class="popup-close">×</span>
                </div>
                <div class="popup-body">
                    <form id="editUserForm">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label for="edit-username">Username</label>
                            <input type="text" id="edit-username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-name">Name</label>
                            <input type="text" id="edit-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-surname">Surname</label>
                            <input type="text" id="edit-surname" name="surname" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-password">Password</label>
                            <input type="password" id="edit-password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="edit-job">Job</label>
                            <select id="edit-job" name="job" required>
                                <option>User</option>
                                <option>Supervisor</option>
                                <option>Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-branch">Branch</label>
                            <select id="edit-branch" name="branch" required>
                                <option value="montana">Montana</option>
                                <option value="zambezi">Zambezi</option>
                                <option value="menlyn">Menlyn</option>
                                <option value="centurion">Centurion</option>
                                <option value="daspoort">Daspoort</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-active">Active</label>
                            <select id="edit-active" name="active" required>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                        <div class="form-group" style="display: flex;justify-content: center;flex-direction: unset;">
                            <button type="submit" id="edit-user-button">Update</button>
                            <button type="button" id="delete-user-button">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
