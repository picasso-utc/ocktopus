<!-- resources/views/payutc.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your App Title</title>
</head>
<body>
    <div class="container">
        <h1>Exemple de requête Payutc</h1>

        <button id="payutcButton">Faire une requête Payutc</button>

        <div id="payutcResponse"></div>
    </div>

    <script>
        document.getElementById('payutcButton').addEventListener('click', function () {
        fetch('/get-goodies-winner')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('payutcResponse').innerText = JSON.stringify(data, null, 2);
        })
        .catch(error => {
            console.error('Erreur lors de la requête Payutc :', error);
            // Display an error message to the user
            document.getElementById('payutcResponse').innerText = 'Erreur lors de la requête Payutc.';
        });
});
    </script>
</body>
</html>
