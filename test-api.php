<!DOCTYPE html>
<html>

<head>
    <title>Test API</title>
</head>

<body>
    <h1>API Test</h1>
    <div id="result">Loading...</div>

    <script>
    async function testAPI() {
        const id = 33;
        const result = document.getElementById('result');

        try {
            result.innerHTML = 'Testing: api/property-details.php?id=' + id + '<br><br>';

            const response = await fetch('api/property-details.php?id=' + id);
            result.innerHTML += 'Status: ' + response.status + '<br>';
            result.innerHTML += 'OK: ' + response.ok + '<br><br>';

            const data = await response.json();
            result.innerHTML += 'Title: ' + data.title + '<br>';
            result.innerHTML += 'Price: ' + data.price + '<br>';
            result.innerHTML += 'Images: ' + JSON.stringify(data.images) + '<br><br>';
            result.innerHTML += '✅ SUCCESS!';

        } catch (error) {
            result.innerHTML += '❌ ERROR: ' + error.message;
        }
    }

    testAPI();
    </script>
</body>

</html>