<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Login and Dashboard</title>
  <style>
    /* General styles */
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    h1 {
      font-size: 24px;
      text-align: center;
    }

    label {
      font-size: 14px;
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }

    button {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      font-weight: bold;
      background: #28a745;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    button:hover {
      background: #218838;
    }

    #responseMessage {
      text-align: center;
      font-size: 14px;
      margin-top: 10px;
    }

    .dashboard-container {
      text-align: center;
    }

    .dashboard-container p {
      font-size: 18px;
    }

    .dashboard-container #userInfo {
      margin-top: 20px;
    }

    /* Logout button styling */
    .logout-button {
      background-color: #dc3545;
      font-weight: bold;
    }

    .logout-button:hover {
      background-color: #c82333;
    }

  </style>
</head>
<body>
  <!-- Login Page -->
  <div class="container" id="loginPage">
    <h1>Login</h1>
    <form id="loginForm">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Enter your email" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required>

      <button type="submit">Login</button>
    </form>
    <p id="responseMessage"></p>
  </div>

  <!-- Dashboard Page (Initially Hidden) -->
  <div class="dashboard-container" id="dashboardPage" style="display: none;">
    <h1>Welcome to your Dashboard</h1>
    <div id="userInfo">
      <p><strong>Username:</strong> <span id="username"></span></p>
      <p><strong>API Key:</strong> <span id="apiKey"></span></p>
    </div>
    <!-- Logout Button -->
    <button class="logout-button" id="logoutButton">Logout</button>
  </div>

  <script>
    // Switch between login and dashboard pages
    const loginPage = document.getElementById('loginPage');
    const dashboardPage = document.getElementById('dashboardPage');

    // Login Form Submission
    document.getElementById('loginForm').addEventListener('submit', async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const data = {
        email: formData.get('email'),
        password: formData.get('password'),
      };

      try {
        const response = await fetch('http://localhost:8000/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data),
        });

        const result = await response.json();
        const messageElement = document.getElementById('responseMessage');

        if (response.ok) {
          messageElement.textContent = `Success: ${result.message}`;
          messageElement.style.color = 'green';

          // Save username and API key in sessionStorage
          sessionStorage.setItem('username', result.username);
          sessionStorage.setItem('api_key', result.api_key);

          // Redirect to the dashboard page
          loginPage.style.display = 'none';
          dashboardPage.style.display = 'block';

          // Display user info on dashboard
          document.getElementById('username').textContent = result.username;
          document.getElementById('apiKey').textContent = result.api_key;
        } else {
          messageElement.textContent = `Error: ${result.message}`;
          messageElement.style.color = 'red';
        }
      } catch (error) {
        document.getElementById('responseMessage').textContent = `Request failed: ${error.message}`;
      }
    });

    // Logout functionality
    document.getElementById('logoutButton').addEventListener('click', function() {
      // Clear sessionStorage and redirect to login page
      sessionStorage.removeItem('username');
      sessionStorage.removeItem('api_key');
      loginPage.style.display = 'block';
      dashboardPage.style.display = 'none';
    });

    // Check if user is logged in and show dashboard
    window.onload = function() {
      const username = sessionStorage.getItem('username');
      const apiKey = sessionStorage.getItem('api_key');

      if (username && apiKey) {
        // User is already logged in, show the dashboard
        loginPage.style.display = 'none';
        dashboardPage.style.display = 'block';

        document.getElementById('username').textContent = username;
        document.getElementById('apiKey').textContent = apiKey;
      } else {
        // User is not logged in, show login page
        loginPage.style.display = 'block';
        dashboardPage.style.display = 'none';
      }
    };
  </script>
</body>
</html>