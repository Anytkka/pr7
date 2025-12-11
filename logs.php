<?php
session_start();
include("./settings/connect_datebase.php");

if (isset($_SESSION['user'])) {
    if($_SESSION['user'] != -1) {
        $user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = ".$_SESSION['user']);
        while($user_read = $user_query->fetch_row()) {
            if($user_read[3] == 0) header("Location: index.php");
        }
    } else header("Location: login.php");
    } else {
    header("Location: login.php");
    echo "Пользователя не существует";
}

include("./settings/session.php");

// Получаем параметры фильтрации
$filter_type = isset($_GET['type']) ? $_GET['type'] : 'all';
$filter_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filter_date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$filter_search = isset($_GET['search']) ? $_GET['search'] : '';

// Формируем SQL с фильтрами
$sql = "SELECT * FROM `logs` WHERE 1=1";

if ($filter_type != 'all') {
    $sql .= " AND `Event` LIKE '%" . $mysqli->real_escape_string($filter_type) . "%'";
}

if (!empty($filter_date_from)) {
    $sql .= " AND DATE(`Date`) >= '" . $mysqli->real_escape_string($filter_date_from) . "'";
}

if (!empty($filter_date_to)) {
    $sql .= " AND DATE(`Date`) <= '" . $mysqli->real_escape_string($filter_date_to) . "'";
}

if (!empty($filter_search)) {
    $sql .= " AND (`Event` LIKE '%" . $mysqli->real_escape_string($filter_search) . "%' 
                  OR `Ip` LIKE '%" . $mysqli->real_escape_string($filter_search) . "%')";
}

$sql .= " ORDER BY `Date`";
$Query = $mysqli->query($sql);

// 1. График: Подробные типы активности пользователей
$detailed_types_sql = "SELECT 
    SUM(CASE WHEN Event LIKE '%авторизовался%' THEN 1 ELSE 0 END) as logins,
    SUM(CASE WHEN Event LIKE '%зарегистрировался%' THEN 1 ELSE 0 END) as registrations,
    SUM(CASE WHEN Event LIKE '%сброшен%' THEN 1 ELSE 0 END) as password_recovery,
    SUM(CASE WHEN Event LIKE '%комментарий%' THEN 1 ELSE 0 END) as comments,
    SUM(CASE WHEN Event LIKE '%покинул%' THEN 1 ELSE 0 END) as logouts
FROM `logs` WHERE 1=1";

if ($filter_type != 'all') {
    $detailed_types_sql .= " AND `Event` LIKE '%" . $mysqli->real_escape_string($filter_type) . "%'";
}

$DetailedTypesQuery = $mysqli->query($detailed_types_sql);
$DetailedTypesData = $DetailedTypesQuery->fetch_assoc();

// 2. График: Динамика активности (дни недели)
$weekdays_sql = "SELECT 
    DAYOFWEEK(Date) as weekday_num,
    DAYNAME(Date) as weekday_name,
    COUNT(*) as count
FROM `logs`";

if ($filter_type != 'all') {
    $weekdays_sql .= " WHERE `Event` LIKE '%" . $mysqli->real_escape_string($filter_type) . "%'";
}

$weekdays_sql .= " GROUP BY DAYOFWEEK(Date), DAYNAME(Date) ORDER BY weekday_num";
$WeekdaysQuery = $mysqli->query($weekdays_sql);

$weekday_labels = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
$weekday_data = [0, 0, 0, 0, 0, 0, 0];

while ($weekday_row = $WeekdaysQuery->fetch_assoc()) {
    // DAYOFWEEK: 1=воскресенье, 2=понедельник...
    $index = $weekday_row['weekday_num'] - 2;
    if ($index < 0) $index = 6; // воскресенье становится 6
    $weekday_data[$index] = $weekday_row['count'];
}

// 3. График: Активность по месяцам
$months_sql = "SELECT 
    MONTH(Date) as month_num,
    MONTHNAME(Date) as month_name,
    COUNT(*) as count
FROM `logs`";

if ($filter_type != 'all') {
    $months_sql .= " WHERE `Event` LIKE '%" . $mysqli->real_escape_string($filter_type) . "%'";
}

$months_sql .= " GROUP BY MONTH(Date), MONTHNAME(Date) ORDER BY month_num";
$MonthsQuery = $mysqli->query($months_sql);

$month_labels = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
$month_data = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

while ($month_row = $MonthsQuery->fetch_assoc()) {
    $month_index = $month_row['month_num'] - 1;
    if ($month_index >= 0 && $month_index < 12) {
        $month_data[$month_index] = $month_row['count'];
    }
}
?>
<!DOCTYPE HTML>
<html>
    <head> 
        <script src="https://code.jquery.com/jquery-1.8.3.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <meta charset="utf-8">
        <title>Детальная аналитика активности - Админ панель</title>
        
        <link rel="stylesheet" href="style.css">

        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                background: white;
            }
            
            table th {
                background: #2c3e50;
                color: white;
                padding: 12px;
                text-align: center;
                border: 1px solid #34495e;
            }
            
            table td {
                padding: 10px;
                text-align: center;
                border: 1px solid #ddd;
            }
            
            table tr:nth-child(even) {
                background: #f9f9f9;
            }
            
            table tr:hover {
                background: #f0f0f0;
            }
            
            .filters {
                margin-bottom: 20px;
                padding: 15px;
                background: #f5f5f5;
                border-radius: 5px;
            }
            .filter-row {
                display: flex;
                gap: 10px;
                margin-bottom: 10px;
                flex-wrap: wrap;
            }
            .filter-group {
                display: flex;
                flex-direction: column;
            }
            .filter-label {
                font-size: 12px;
                color: #666;
                margin-bottom: 3px;
            }
            .filter-select, .filter-input {
                padding: 5px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }
            .filter-button {
                padding: 5px 15px;
                background: #0066cc;
                color: white;
                border: none;
                border-radius: 3px;
                cursor: pointer;
                height: 29px;
                align-self: flex-end;
            }
            .filter-button:hover {
                background: #0055aa;
            }
            .status-online {
                color: #27ae60;
                font-weight: bold;
            }
            .no-data {
                text-align: center;
                padding: 20px;
                color: #666;
                font-style: italic;
            }
            
            .chart-container {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin: 20px 0;
            }
            
            .chart-box {
                background: white;
                padding: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
                height: 320px;
            }
            
            .chart-title {
                font-size: 14px;
                font-weight: bold;
                margin-bottom: 15px;
                text-align: center;
                color: #333;
            }
            
            @media (max-width: 1200px) {
                .chart-container {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <div class="top-menu">

            <a href=#><img src = "img/logo1.png"/></a>
            <div class="name">
                <a href="index.php">
                    <div class="subname">БЗОПАСНОСТЬ  ВЕБ-ПРИЛОЖЕНИЙ</div>
                    Пермский авиационный техникум им. А. Д. Швецова
                </a>
            </div>
        </div>
        <div class="space"> </div>
        <div class="main">
            <div class="content">
                <div class="name">Детальная аналитика активности пользователей</div>
                
                <!-- Форма фильтрации -->
                <div class="filters">
                    <form method="GET" action="">
                        <div class="filter-row">
                            <div class="filter-group">
                                <div class="filter-label">Тип события</div>
                                <select class="filter-select" name="type">
                                    <option value="all" <?php echo $filter_type == 'all' ? 'selected' : ''; ?>>Все события</option>
                                    <option value="авторизовался" <?php echo $filter_type == 'авторизовался' ? 'selected' : ''; ?>>Авторизация</option>
                                    <option value="зарегистрировался" <?php echo $filter_type == 'зарегистрировался' ? 'selected' : ''; ?>>Регистрация</option>
                                    <option value="сброшен" <?php echo $filter_type == 'сброшен' ? 'selected' : ''; ?>>Восстановление пароля</option>
                                    <option value="комментарий" <?php echo $filter_type == 'комментарий' ? 'selected' : ''; ?>>Комментарии</option>
                                    <option value="покинул" <?php echo $filter_type == 'покинул' ? 'selected' : ''; ?>>Выход</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <div class="filter-label">Дата с</div>
                                <input type="date" class="filter-input" name="date_from" value="<?php echo $filter_date_from; ?>">
                            </div>
                            
                            <div class="filter-group">
                                <div class="filter-label">Дата по</div>
                                <input type="date" class="filter-input" name="date_to" value="<?php echo $filter_date_to; ?>">
                            </div>
                            
                            <button type="submit" class="filter-button">Применить</button>
                            <button type="button" class="filter-button" onclick="resetFilters()">Сбросить</button>
                        </div>
                    </form>
                </div>

                <!-- Таблица событий  -->
                <table border="1">
                    <tr>
                        <td style="width: 165px;">Дата и время</td>
                        <td style="width: 165px;">IP пользователя</td>
                        <td style="width: 165px;">Время в сети</td>
                        <td style="width: 165px;">Статус</td>
                        <td>Произошедшее событие</td>
                    </tr>
                    
                    <?php
                    if ($Query->num_rows > 0) {
                        while($Read = $Query->fetch_assoc()) {
                            $Status = "";
                            $SqlSession = "SELECT * FROM `session` WHERE `IdUser` = {$Read["IdUser"]} ORDER BY `DateStart` DESC";
                            $QuerySession = $mysqli->query($SqlSession);
                            
                            if ($QuerySession->num_rows > 0) {
                                $ReadSession = $QuerySession->fetch_assoc();
                                $TimeEnd = strtotime($ReadSession["DateNow"]) + 5*60;
                                $TimeNow = time();

                                if($TimeEnd > $TimeNow) {
                                    $Status = "online";
                                    $status_class = "status-online";
                                } else {
                                    $TimeEnd = strtotime($ReadSession["DateNow"]);
                                    $TimeDelta = round(($TimeNow - $TimeEnd)/60);
                                    $Status = "Был в сети: {$TimeDelta} минут назад";
                                    $status_class = "";
                                }
                            } else {
                                $Status = "Никогда не был онлайн";
                                $status_class = "";
                            }
                            
                            echo "<tr>";
                            echo "<td>{$Read["Date"]}</td>";
                            echo "<td>{$Read["Ip"]}</td>";
                            echo "<td>{$Read["TimeOnline"]}</td>";
                            echo "<td class='{$status_class}'>{$Status}</td>";
                            echo "<td style='text-align: left'>{$Read["Event"]}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='no-data'>События не найдены</td></tr>";
                    }
                    ?>
                </table>

                <!-- диаграммы активности -->
                <div class="chart-container">
                    <div class="chart-box">
                        <div class="chart-title">Детальное распределение типов событий</div>
                        <canvas id="detailedTypesChart"></canvas>
                    </div>
                    
                    <div class="chart-box">
                        <div class="chart-title">Активность по дням недели</div>
                        <canvas id="weekdaysChart"></canvas>
                    </div>
                    
                    <div class="chart-box">
                        <div class="chart-title">Активность по месяцам</div>
                        <canvas id="monthsChart"></canvas>
                    </div>
                </div>
            
                <div class="footer">
                    © КГАПОУ "Авиатехникум", 2020
                    <a href=#>Конфиденциальность</a>
                    <a href=#>Условия</a>
                </div>
            </div>
        </div>
        
        <script>
            // Данные для графиков
            const detailedTypesLabels = ['Авторизации', 'Регистрации', 'Восстановление пароля', 'Комментарии', 'Выходы из системы'];
            const detailedTypesData = [
                <?php echo $DetailedTypesData['logins'] ?? 0; ?>,
                <?php echo $DetailedTypesData['registrations'] ?? 0; ?>,
                <?php echo $DetailedTypesData['password_recovery'] ?? 0; ?>,
                <?php echo $DetailedTypesData['comments'] ?? 0; ?>,
                <?php echo $DetailedTypesData['logouts'] ?? 0; ?>
            ];
            
            const weekdaysLabels = <?php echo json_encode($weekday_labels); ?>;
            const weekdaysData = <?php echo json_encode($weekday_data); ?>;
            
            const monthsLabels = <?php echo json_encode($month_labels); ?>;
            const monthsData = <?php echo json_encode($month_data); ?>;
            
            function initCharts() {
                // 1. Детальное распределение типов событий (столбчатая диаграмма)
                const ctx1 = document.getElementById('detailedTypesChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: detailedTypesLabels,
                        datasets: [{
                            label: 'Количество событий',
                            data: detailedTypesData,
                            backgroundColor: [
                                '#3498db', // синий - авторизации
                                '#2ecc71', // зеленый - регистрации
                                '#e74c3c', // красный - восстановление пароля
                                '#f39c12', // оранжевый - комментарии
                                '#9b59b6'  // фиолетовый - выходы
                            ],
                            borderColor: [
                                '#2980b9',
                                '#27ae60',
                                '#c0392b',
                                '#d35400',
                                '#8e44ad'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Количество событий'
                                }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
                
                // 2. Активность по дням недели (линейный график)
                const ctx2 = document.getElementById('weekdaysChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: weekdaysLabels,
                        datasets: [{
                            label: 'Событий в день',
                            data: weekdaysData,
                            borderColor: '#9b59b6',
                            backgroundColor: 'rgba(155, 89, 182, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#8e44ad',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Количество событий'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'День недели'
                                }
                            }
                        }
                    }
                });
                
                // 3. Активность по месяцам (область с заливкой)
                const ctx3 = document.getElementById('monthsChart').getContext('2d');
                new Chart(ctx3, {
                    type: 'line',
                    data: {
                        labels: monthsLabels,
                        datasets: [{
                            label: 'Событий в месяц',
                            data: monthsData,
                            borderColor: '#e74c3c',
                            backgroundColor: 'rgba(231, 76, 60, 0.2)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 3,
                            pointBackgroundColor: '#c0392b',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Количество событий'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Месяц'
                                }
                            }
                        }
                    }
                });
            }
            
            function resetFilters() {
                window.location.href = 'logs.php';
            }
            
            // Инициализируем графики при загрузке страницы
            $(document).ready(function() {
                initCharts();
            });
        </script>
    </body>
</html>