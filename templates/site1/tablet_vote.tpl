<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vote</title>
    <style>
        body {
            text-align: center;
            font-family: sans-serif;
            color: #888;
            line-height: 24px;
            height: 100vh;
            margin: 0;
            padding: 0;
        }
        .flex {
            display:flex;
            display: -webkit-flex; /* Safari */
            -webkit-align-items: center;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            height: 100%;
        }
        .vote_list {
            display:flex;
            display: -webkit-flex; /* Safari */
            -webkit-align-items: center;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }
        .vote_list > a {
            display: block;
            flex: 1;
            -webkit-flex: 1;
            text-align: center;
            text-decoration: none;
            color: #000;
        }
        .vote_list > a > img {
            width: 65px;
        }
        .vote_list > a > span {
            display: block;
        }
        .tablet {
            width: 60%;
        }
        img {
            width: 300px;
            max-width: 100%;
        }
        .vote_table {
            border:1px solid #e3e3e3;
            border-radius:5px 5px 5px 5px;
            background-color:#f3f3f3;
            padding: 10px 20px;
            text-align: left;
        }
        .agent {
            float: right;
            width: auto;
            margin-left: 10px;
            width: 200px;
        }
        h1 {
            color: #27b7c7;
        }
		h2 {
			font-size: 18px;
		}
        p:after {
            content: '';
            display: block;
            clear: both;
        }

        @media (max-width: 1024px) {
            .tablet {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="flex">
        <div class="tablet">
            
            <div class="vote_table">
                <img src="{theme}/img/logo.svg" class="agent">
                <h1>Dear {name}!</h1>
                <p>
                    Thank you for your recent visit to Your Company.
                </p>
                <h2>
                    How likely is it that you would recommend this company to a friend or colleague?
                </h2>
                <div class="vote_list">
                    <a href="#">
                        <img src="https://yoursite.com/feedback/images/rate_1.png">
                        <span>No, never</span>
                    </a>
                    <a href="#">
                        <img src="https://yoursite.com/feedback/images/rate_2.png">
                        <span>I'm not sure</span>
                    </a>
                    <a href="#">
                        <img src="https://yoursite.com/feedback/images/rate_3.png">
                        <span>Maybe</span>
                    </a>
                    <a href="#">
                        <img src="https://yoursite.com/feedback/images/rate_4.png">
                        <span>I will</span>
                    </a>
                    <a href="#">
                        <img src="https://yoursite.com/feedback/images/rate_5.png">
                        <span>Definitely</span>
                    </a>
                </div>
                <p>
                    Sincerely yours<br> 
                    Your Company inc.
                </p>
            </div>
        </div>
    </div>
</body>
</html>