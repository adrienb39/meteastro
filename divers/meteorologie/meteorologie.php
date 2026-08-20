<?php
session_start();
require_once '../../config/connexion_bdd.php';
$db = createPdoConnection();

function getMeteorologieContent($db)
{
    $sql = "SELECT m.*, u.name FROM meteorologie m
            INNER JOIN users u ON m.id_users = u.id_users 
            WHERE m.verified = 'y' ORDER BY m.date_meteorologie DESC";
    try {
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
$posts = getMeteorologieContent($db);
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meteastro | Radar & Communauté</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=JetBrains+Mono&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            --bg-dark: #06090f;
            --panel-bg: rgba(13, 17, 23, 0.85);
            --accent: #00d2ff;
            --danger: #ff4d4d;
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-main: #e6edf3;
        }

        body {
            background: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        /* Background Dynamique */
        #weather-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            transition: background 1.5s ease-in-out;
            background-size: cover;
            background-position: center;
        }

        .bg-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(6, 9, 15, 0.4) 0%, rgba(6, 9, 15, 1) 100%);
        }

        /* Layout Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 20px;
            padding: 20px;
            max-width: 1800px;
            margin: 0 auto;
            position: relative;
        }

        #map {
            height: 600px;
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            z-index: 1;
        }

        .widget {
            background: var(--panel-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 25px;
            backdrop-filter: blur(12px);
        }

        .search-box {
            width: 100%;
            padding: 15px 20px;
            border-radius: 15px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid var(--glass-border);
            color: white;
            margin-bottom: 20px;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        .search-box:focus {
            border-color: var(--accent);
        }

        .risk-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .risk-low {
            background: #238636;
            color: white;
        }

        .risk-high {
            background: var(--danger);
            color: white;
            box-shadow: 0 0 15px var(--danger);
        }

        .temp-main {
            font-size: 4rem;
            font-weight: 800;
            color: var(--accent);
            letter-spacing: -2px;
        }

        .forecast-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .forecast-day {
            background: rgba(255, 255, 255, 0.05);
            padding: 12px;
            border-radius: 18px;
            text-align: center;
            font-size: 0.8rem;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
        }

        .modal-content {
            background: #0d1117;
            margin: 10% auto;
            padding: 40px;
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            max-width: 650px;
            position: relative;
        }

        .close-btn {
            position: absolute;
            right: 25px;
            top: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #8b949e;
        }

        .report-item {
            padding: 15px;
            border-bottom: 1px solid var(--glass-border);
            cursor: pointer;
            transition: 0.2s;
        }

        .report-item:hover {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
        }

        @media (max-width: 1100px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        #weather-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            transition: all 1.5s ease-in-out;
            background-color: #06090f;
        }

        /* Effet de Soleil (Halo) */
        .sun-glow {
            position: absolute;
            top: -10%;
            right: -10%;
            width: 60vw;
            height: 60vw;
            background: radial-gradient(circle, rgba(255, 200, 0, 0.2) 0%, rgba(255, 150, 0, 0) 70%);
            display: none;
            /* Activé en JS */
            animation: pulse 8s infinite alternate;
        }

        @keyframes pulse {
            from {
                transform: scale(1);
                opacity: 0.5;
            }

            to {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }

        /* Style des gouttes de pluie ou flocons */
        .particle {
            position: absolute;
            background: white;
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>

<body>

    <div id="weather-bg">
        <div class="bg-overlay"></div>
        <div id="particles-container"></div>
        <div class="sun-glow"></div>
    </div>

    <?php include "../../__partials/menu.php"; ?>

    <div id="reportModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 id="m-title" style="color: var(--accent); margin-top: 0;"></h2>
            <div style="margin-bottom: 20px; font-size: 0.9rem; color: #8b949e;">
                <i class="fa-solid fa-user-astronaut"></i> <span id="m-author"></span> •
                <i class="fa-solid fa-clock"></i> <span id="m-date"></span>
            </div>
            <div id="m-body" style="line-height: 1.7; font-size: 1.05rem;"></div>
        </div>
    </div>

    <div class="main-grid">
        <div class="left-col">
            <div id="map"></div>
            <div class="widget" style="margin-top: 20px;">
                <h3 style="margin-top:0; font-size: 1.1rem;"><i class="fa-solid fa-clock-rotate-left"></i> Tendances 5
                    Prochains Jours</h3>
                <div class="forecast-grid" id="forecast-container"></div>
            </div>
        </div>

        <div class="sidebar">
            <div class="widget">
                <input type="text" id="city-search" class="search-box" placeholder="Rechercher une ville (ex: Lyon)...">
                <div id="weather-display">
                    <div id="risk-badge" class="risk-badge risk-low">Initialisation...</div>
                    <h2 id="city-name" style="margin: 0; font-size: 2rem;">--</h2>
                    <p id="weather-desc" style="color: #8b949e; text-transform: capitalize; margin: 5px 0 20px 0;">--
                    </p>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div class="temp-main"><span id="main-temp">--</span>°</div>
                        <img id="weather-icon" src="" style="width: 100px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px;">
                        <div
                            style="background: rgba(255,255,255,0.03); padding: 10px; border-radius: 12px; font-size: 0.8rem;">
                            <i class="fa-solid fa-wind" style="color: var(--accent)"></i> VENT: <span
                                id="val-wind">--</span> km/h
                        </div>
                        <div
                            style="background: rgba(255,255,255,0.03); padding: 10px; border-radius: 12px; font-size: 0.8rem;">
                            <i class="fa-solid fa-droplet" style="color: var(--accent)"></i> HUM: <span
                                id="val-hum">--</span>%
                        </div>
                    </div>
                </div>
            </div>

            <div class="widget" style="margin-top: 20px; max-height: 400px; overflow-y: auto;">
                <h4 style="margin-top: 0; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px;">
                    <i class="fa-solid fa-tower-broadcast"></i> FLUX COMMUNAUTÉ
                </h4>
                <?php foreach ($posts as $post): ?>
                    <a class="report-item" href="contenu-meteorologie.php?id=<?= $post['id'] ?>"
    data-title="<?= htmlspecialchars($post['title_contenu']) ?>"
    data-author="<?= htmlspecialchars($post['name']) ?>"
    data-date="<?= date("d M Y", strtotime($post['date_meteorologie'])) ?>"
    data-body="<?= nl2br(htmlspecialchars($post['contenu'])) ?>"
    data-img="../../uploads/<?= $post['filename']; ?>" 
    style="display: flex; align-items: center; gap: 12px; text-decoration: none; margin-bottom: 15px;">
    
    <div style="width: 50px; height: 50px; border-radius: 4px; overflow: hidden; flex-shrink: 0; background: #21262d;">
        <img src="../../uploads/<?= $post['filename']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
    </div>

    <div>
        <div style="font-weight: 600; font-size: 0.9rem; color: #c9d1d9;">
            <?= htmlspecialchars($post['title_contenu']) ?>
        </div>
        <div style="font-size: 0.75rem; color: #8b949e;">
            Par <?= htmlspecialchars($post['name']) ?>
        </div>
    </div>
</a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php include "../../cookie/cookie.php"; ?>
    <?php include "../../__partials/footer.php"; ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_KEY = '35435894e047a1125ad6ef5ff1425ed6';
        const DEFAULT_CITY = '';

        // --- INITIALISATION CARTE ---
        const map = L.map('map', { zoomControl: false, attributionControl: false }).setView([46.6, 1.8], 5);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map);

        const layers = {
            "Précipitations": L.tileLayer(`https://tile.openweathermap.org/map/precipitation_new/{z}/{x}/{y}.png?appid=${API_KEY}`),
            "Vent": L.tileLayer(`https://tile.openweathermap.org/map/wind_new/{z}/{x}/{y}.png?appid=${API_KEY}`)
        };
        layers["Précipitations"].addTo(map);
        L.control.layers(null, layers, { collapsed: false, position: 'topright' }).addTo(map);

        // --- MOTEUR D'ANIMATION MÉTÉO ---
        let lightningInterval;

        function updateDynamicBackground(condition) {
            const bg = document.getElementById('weather-bg');
            let pContainer = document.getElementById('particles-container');

            if (!pContainer) {
                pContainer = document.createElement('div');
                pContainer.id = 'particles-container';
                bg.appendChild(pContainer);
            }

            // Reset complet
            pContainer.innerHTML = '';
            clearInterval(lightningInterval);
            bg.style.filter = 'none';
            bg.className = '';

            switch (condition) {
                case 'Thunderstorm':
                    // Fond très sombre avec flashs
                    bg.style.background = "linear-gradient(180deg, #0f0c29 0%, #302b63 50%, #24243e 100%)";
                    createParticles(pContainer, 'rain', 80);
                    startLightning();
                    break;

                case 'Rain':
                case 'Drizzle':
                    bg.style.background = "linear-gradient(180deg, #203a43 0%, #2c5364 100%)";
                    createParticles(pContainer, 'rain', 120);
                    break;

                case 'Snow':
                    bg.style.background = "linear-gradient(180deg, #e6dada 0%, #274046 100%)";
                    createParticles(pContainer, 'snow', 100);
                    break;

                case 'Clear':
                    // Effet "Grand Soleil" avec halo
                    bg.style.background = "radial-gradient(circle at 50% -10%, #ffcc00 0%, #ff9500 20%, #06090f 70%)";
                    createParticles(pContainer, 'star', 30);
                    break;

                case 'Clouds':
                    // Effet brumeux / nuageux
                    bg.style.background = "linear-gradient(180deg, #3c3c3c 0%, #111 100%)";
                    createClouds(pContainer);
                    break;

                default:
                    bg.style.background = "#06090f";
            }
        }

        function createParticles(container, type, count) {
            for (let i = 0; i < count; i++) {
                const p = document.createElement('div');
                const x = Math.random() * 100;
                const duration = Math.random() * 1 + 0.5;
                const delay = Math.random() * 2;

                p.style.position = 'absolute';
                p.style.left = `${x}%`;
                p.style.top = '-5%';
                p.style.pointerEvents = 'none';

                if (type === 'rain') {
                    p.style.width = '1px';
                    p.style.height = '25px';
                    p.style.background = 'rgba(255,255,255,0.3)';
                    p.animate([
                        { transform: 'translateY(0vh)' },
                        { transform: 'translateY(105vh)' }
                    ], { duration: duration * 400, iterations: Infinity, delay: delay * 1000 });
                } else if (type === 'snow') {
                    const size = Math.random() * 6 + 2;
                    p.style.width = `${size}px`;
                    p.style.height = `${size}px`;
                    p.style.background = '#fff';
                    p.style.borderRadius = '50%';
                    p.style.filter = 'blur(1px)';
                    p.animate([
                        { transform: `translate(0, 0)`, opacity: 0.8 },
                        { transform: `translate(${Math.random() * 100 - 50}px, 105vh)`, opacity: 0.2 }
                    ], { duration: duration * 3000, iterations: Infinity, delay: delay * 1000 });
                } else if (type === 'star') {
                    p.style.top = Math.random() * 100 + '%';
                    p.style.width = '2px';
                    p.style.height = '2px';
                    p.style.background = '#fff';
                    p.animate([{ opacity: 0.2 }, { opacity: 1 }, { opacity: 0.2 }], { duration: 2000, iterations: Infinity });
                }
                container.appendChild(p);
            }
        }

        function createClouds(container) {
            for (let i = 0; i < 6; i++) {
                const cloud = document.createElement('div');
                cloud.style.cssText = `
            position: absolute;
            width: 600px; height: 300px;
            background: rgba(255,255,255,0.05);
            filter: blur(60px);
            border-radius: 50%;
            top: ${Math.random() * 50}%;
            left: ${Math.random() * 100}%;
        `;
                cloud.animate([
                    { transform: 'translateX(-200px)' },
                    { transform: 'translateX(200px)' }
                ], { duration: 10000 + (i * 2000), iterations: Infinity, direction: 'alternate' });
                container.appendChild(cloud);
            }
        }

        function startLightning() {
            const bg = document.getElementById('weather-bg');
            lightningInterval = setInterval(() => {
                if (Math.random() > 0.93) {
                    bg.style.filter = 'brightness(4) saturate(2)';
                    setTimeout(() => { bg.style.filter = 'none'; }, 50)
                    setTimeout(() => { bg.style.filter = 'brightness(2)'; }, 150)
                    setTimeout(() => { bg.style.filter = 'none'; }, 200)
                }
            }, 1000);
        }

        // --- APPELS API ---
        async function fetchWeather(city = "") {
            const query = city || DEFAULT_CITY;
            try {
                const [resCur, resFore] = await Promise.all([
                    fetch(`https://api.openweathermap.org/data/2.5/weather?q=${query}&units=metric&lang=fr&appid=${API_KEY}`),
                    fetch(`https://api.openweathermap.org/data/2.5/forecast?q=${query}&units=metric&lang=fr&appid=${API_KEY}`)
                ]);

                const data = await resCur.json();
                const dataF = await resFore.json();

                if (data.cod === 200) {
                    document.getElementById('city-name').innerText = data.name;
                    document.getElementById('main-temp').innerText = Math.round(data.main.temp);
                    document.getElementById('weather-desc').innerText = data.weather[0].description;
                    document.getElementById('weather-icon').src = `https://openweathermap.org/img/wn/${data.weather[0].icon}@4x.png`;
                    document.getElementById('val-wind').innerText = Math.round(data.wind.speed * 3.6);
                    document.getElementById('val-hum').innerText = data.main.humidity;

                    const badge = document.getElementById('risk-badge');
                    const isDangerous = data.weather[0].main === 'Thunderstorm' || data.wind.speed > 15;
                    badge.innerText = isDangerous ? "ALERTE MÉTÉO" : "SITUATION CALME";
                    badge.className = `risk-badge ${isDangerous ? 'risk-high' : 'risk-low'}`;

                    map.flyTo([data.coord.lat, data.coord.lon], 10);
                    updateDynamicBackground(data.weather[0].main);

                    const container = document.getElementById('forecast-container');
                    container.innerHTML = '';
                    dataF.list.filter(f => f.dt_txt.includes("12:00:00")).forEach(day => {
                        const date = new Date(day.dt * 1000).toLocaleDateString('fr-FR', { weekday: 'short' });
                        container.innerHTML += `
                    <div class="forecast-day">
                        <div style="font-weight:800;">${date.toUpperCase()}</div>
                        <img src="https://openweathermap.org/img/wn/${day.weather[0].icon}.png" style="width:40px">
                        <div style="color:var(--accent); font-weight:bold">${Math.round(day.main.temp)}°</div>
                    </div>`;
                    });
                }
            } catch (e) { console.error("Erreur météo:", e); }
        }

        function openModal(el) {
            document.getElementById('m-title').innerText = el.dataset.title;
            document.getElementById('m-author').innerText = el.dataset.author;
            document.getElementById('m-date').innerText = el.dataset.date;
            document.getElementById('m-body').innerHTML = el.dataset.body;
            document.getElementById('reportModal').style.display = 'block';
        }

        function closeModal() { document.getElementById('reportModal').style.display = 'none'; }

        document.getElementById('city-search').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') fetchWeather(e.target.value);
        });

        window.onload = () => fetchWeather();
        window.onclick = (e) => { if (e.target.className === 'modal') closeModal(); };
    </script>
</body>

</html>