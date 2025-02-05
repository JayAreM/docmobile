<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Activity</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8faff;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .card {
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 400px;
            padding: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ececec;
            padding-bottom: 10px;
        }

        .card-header h2 {
            font-size: 18px;
            margin: 0;
            font-weight: 600;
            color: #333;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-btn {
            border: none;
            background: none;
            cursor: pointer;
            font-size: 16px;
            color: #888;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 30px;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            overflow: hidden;
            display: none;
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu div {
            padding: 10px 15px;
            font-size: 14px;
            color: #555;
            cursor: pointer;
        }

        .dropdown-menu div:hover {
            background-color: #f4f4f4;
        }

        .activity-list {
            margin-top: 20px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .activity-item:last-child {
            margin-bottom: 0;
        }

        .activity-time {
            font-size: 14px;
            color: #888;
            width: 50px;
            text-align: right;
            margin-right: 15px;
        }

        .activity-marker {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .activity-text {
            font-size: 14px;
            color: #555;
        }

        .activity-text strong {
            font-weight: bold;
            color: #333;
        }

        .marker-green { background-color: #28a745; }
        .marker-red { background-color: #dc3545; }
        .marker-blue { background-color: #007bff; }
        .marker-yellow { background-color: #ffc107; }
        .marker-gray { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h2>Recent Activity | Today</h2>
            <div class="dropdown">
                <button class="dropdown-btn">...</button>
                <div class="dropdown-menu">
                    <div>Today</div>
                    <div>This Month</div>
                    <div>This Year</div>
                </div>
            </div>
        </div>

        <div class="activity-list">
            <div class="activity-item">
                <div class="activity-time">32 min</div>
                <div class="activity-marker marker-green"></div>
                <div class="activity-text">Quia quae rerum <strong>explicabo officiis</strong>.</div>
            </div>

            <div class="activity-item">
                <div class="activity-time">56 min</div>
                <div class="activity-marker marker-red"></div>
                <div class="activity-text">Voluptatem blanditiis blanditiis eveniet.</div>
            </div>

            <div class="activity-item">
                <div class="activity-time">2 hrs</div>
                <div class="activity-marker marker-blue"></div>
                <div class="activity-text">Voluptates corrupti molestias voluptatum.</div>
            </div>

            <div class="activity-item">
                <div class="activity-time">1 day</div>
                <div class="activity-marker marker-blue"></div>
                <div class="activity-text">Tempore autem saepe <strong>occaecati voluptas</strong>.</div>
            </div>

            <div class="activity-item">
                <div class="activity-time">2 days</div>
                <div class="activity-marker marker-yellow"></div>
                <div class="activity-text">Est sit eum reiciendis exercitationem.</div>
            </div>

            <div class="activity-item">
                <div class="activity-time">4 weeks</div>
                <div class="activity-marker marker-gray"></div>
                <div class="activity-text">Dicta dolorem harum nulla eius. Ut quidem quidem sit quas.</div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('.dropdown-btn').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });
    </script>
</body>
</html>
