<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>API Test</title>
</head>
<body>
  <h1>Test API Form</h1>
  <form id="apiForm">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required><br><br>

    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea><br><br>

    <button type="submit">Submit</button>
  </form>

  <div id="response">
    <h2>Response:</h2>
    <pre id="responseData"></pre>
  </div>

  <script>
    document.getElementById('apiForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const title = document.getElementById('title').value;
      const description = document.getElementById('description').value;

      try {
        const response = await fetch('https://port-folio-api-leyl.vercel.app/projects', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer YOUR_API_KEY' // Replace with your actual API key
          },
          body: JSON.stringify({ title, description })
        });

        const responseData = await response.json();
        document.getElementById('responseData').textContent = JSON.stringify(responseData, null, 2);
      } catch (error) {
        document.getElementById('responseData').textContent = `Error: ${error.message}`;
      }
    });
  </script>
</body>
</html>