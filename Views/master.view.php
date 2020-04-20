<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
</head>

<body>
    <style>
        * {
            font-family: "Segoe UI", Arial;
            margin: 0px;
            padding: 0px;
            box-sizing: border-box
        }

        .row {
            display: flex;
            justify-content: center
        }

        .col {
            width: 100%
        }

        img {
            width: 100%;
            max-width: 400px;
            margin: 10px
        }

        a:link,
        a:visited {
            color: #000;
            text-decoration: none;
            margin: 0px 10px;
            padding: 20px 30px;
            font-weight: bold
        }

        a:hover {
            text-decoration: underline
        }
    </style>
    <?php include($include . ".php") ?>
</body>

</html>