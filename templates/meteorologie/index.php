<?php
$posts = $posts ?? [];
$escape = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<link
    href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=JetBrains+Mono&display=swap"
    rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    :root {
        --bg-dark: #06090f;
        --panel-bg: rgba(13, 17, 23, .85);
        --accent: #00d2ff;
        --danger: #ff4d4d;
        --glass-border: rgba(255, 255, 255, .08);
        --text-main: #e6edf3
    }

    .meteorologie-page {
        min-height: 100vh;
        background: var(--bg-dark);
        color: var(--text-main);
        font-family: 'Plus Jakarta Sans', sans-serif;
        overflow: hidden
    }

    .weather-bg {
        position: fixed;
        inset: 0;
        z-index: 0;
        transition: all 1.5s ease-in-out;
        background: #06090f;
        z-index: -1
    }

    .bg-overlay {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle, rgba(6, 9, 15, .4), rgba(6, 9, 15, 1))
    }

    .meteorologie-content {
        position: relative;
        z-index: 1
    }

    .main-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 20px;
        padding: 20px;
        max-width: 1800px;
        margin: 0 auto
    }

    .map {
        height: 600px;
        border-radius: 24px;
        border: 1px solid var(--glass-border);
        z-index: 1
    }

    .widget {
        background: var(--panel-bg);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: 25px;
        backdrop-filter: blur(12px)
    }

    .search-box {
        width: 100%;
        padding: 15px 20px;
        border-radius: 15px;
        background: rgba(0, 0, 0, .5);
        border: 1px solid var(--glass-border);
        color: #fff;
        margin-bottom: 20px;
        font-size: 1rem;
        outline: none
    }

    .search-box:focus {
        border-color: var(--accent)
    }

    .risk-badge {
        display: inline-block;
        padding: 6px 15px;
        border-radius: 50px;
        font-size: .7rem;
        font-weight: 800;
        margin-bottom: 15px;
        text-transform: uppercase
    }

    .risk-low {
        background: #238636;
        color: #fff
    }

    .risk-high {
        background: var(--danger);
        color: #fff;
        box-shadow: 0 0 15px var(--danger)
    }

    .temp-main {
        font-size: 4rem;
        font-weight: 800;
        color: var(--accent)
    }

    .forecast-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin-top: 15px
    }

    .forecast-day {
        background: rgba(255, 255, 255, .05);
        padding: 12px;
        border-radius: 18px;
        text-align: center;
        font-size: .8rem
    }

    .report-item {
        padding: 15px;
        border-bottom: 1px solid var(--glass-border);
        cursor: pointer;
        transition: .2s
    }

    .report-item:hover {
        background: rgba(255, 255, 255, .05);
        border-radius: 12px
    }

    @media(max-width:1100px) {
        .main-grid {
            grid-template-columns: 1fr
        }
    }

    @media(max-width:600px) {
        .forecast-grid {
            grid-template-columns: repeat(2, 1fr)
        }

        .main-grid {
            padding: 10px
        }

        .map {
            height: 420px
        }
    }
</style>
<div class="meteorologie-page">
    <div id="weather-bg" class="weather-bg">
        <div class="bg-overlay"></div>
        <div id="particles-container"></div>
        <div class="sun-glow"></div>
    </div>
    <div class="meteorologie-content">
        <div class="main-grid">
            <div class="left-col">
                <div id="map" class="map"></div>
                <div class="widget" style="margin-top:20px">
                    <h3 style="margin-top:0;font-size:1.1rem"><i class="fa-solid fa-clock-rotate-left"></i> Tendances 5
                        Prochains Jours</h3>
                    <div class="forecast-grid" id="forecast-container"></div>
                </div>
            </div>
            <div class="sidebar">
                <div class="widget"><input type="text" id="city-search" class="search-box"
                        placeholder="Rechercher une ville (ex: Lyon)...">
                    <div id="weather-display">
                        <div id="risk-badge" class="risk-badge risk-low">Initialisation...</div>
                        <h2 id="city-name" style="margin:0;font-size:2rem">--</h2>
                        <p id="weather-desc" style="color:#8b949e;text-transform:capitalize;margin:5px 0 20px">--</p>
                        <div style="display:flex;align-items:center;justify-content:space-between">
                            <div class="temp-main"><span id="main-temp">--</span>°</div><img id="weather-icon" src=""
                                style="width:100px">
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:20px">
                            <div
                                style="background:rgba(255,255,255,.03);padding:10px;border-radius:12px;font-size:.8rem">
                                <i class="fa-solid fa-wind" style="color:var(--accent)"></i> VENT: <span
                                    id="val-wind">--</span> km/h
                            </div>
                            <div
                                style="background:rgba(255,255,255,.03);padding:10px;border-radius:12px;font-size:.8rem">
                                <i class="fa-solid fa-droplet" style="color:var(--accent)"></i> HUM: <span
                                    id="val-hum">--</span>%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="widget" style="margin-top:20px;max-height:400px;overflow-y:auto">
                    <h4 style="margin-top:0;border-bottom:1px solid var(--glass-border);padding-bottom:10px"><i
                            class="fa-solid fa-tower-broadcast"></i> FLUX COMMUNAUTÉ</h4>
                    <?php foreach ($posts as $post): ?><a class="report-item" href="/meteorologie/<?= $post['id'] ?>"
                            data-title="<?= $escape($post['title_contenu']) ?>" data-author="<?= $escape($post['name']) ?>"
                            data-date="<?= $post['date_meteorologie'] instanceof DateTimeInterface ? $post['date_meteorologie']->format('d M Y') : $escape($post['date_meteorologie']) ?>"
                            data-body="<?= $escape(nl2br($post['contenu'])) ?>"
                            style="display:flex;align-items:center;gap:12px;text-decoration:none;margin-bottom:15px">
                            <div
                                style="width:50px;height:50px;border-radius:4px;overflow:hidden;flex-shrink:0;background:#21262d">
                                <img src="/uploads/<?= $escape($post['filename']) ?>"
                                    style="width:100%;height:100%;object-fit:cover">
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.9rem;color:#c9d1d9">
                                    <?= $escape($post['title_contenu']) ?>
                                </div>
                                <div style="font-size:.75rem;color:#8b949e">Par <?= $escape($post['name']) ?></div>
                            </div>
                        </a><?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const API_KEY = '35435894e047a1125ad6ef5ff1425ed6', DEFAULT_CITY = ''; const map = L.map('map', { zoomControl: false, attributionControl: false }).setView([46.6, 1.8], 5); L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map); const layers = { 'Précipitations': L.tileLayer(`https://tile.openweathermap.org/map/precipitation_new/{z}/{x}/{y}.png?appid=${API_KEY}`), 'Vent': L.tileLayer(`https://tile.openweathermap.org/map/wind_new/{z}/{x}/{y}.png?appid=${API_KEY}`) }; layers['Précipitations'].addTo(map); L.control.layers(null, layers, { collapsed: false, position: 'topright' }).addTo(map); let lightningInterval; function updateDynamicBackground(condition) { const bg = document.getElementById('weather-bg'), c = document.getElementById('particles-container'); c.innerHTML = ''; clearInterval(lightningInterval); bg.style.filter = 'none'; const colors = { Thunderstorm: 'linear-gradient(180deg,#0f0c29,#302b63 50%,#24243e)', Rain: 'linear-gradient(180deg,#203a43,#2c5364)', Drizzle: 'linear-gradient(180deg,#203a43,#2c5364)', Snow: 'linear-gradient(180deg,#e6dada,#274046)', Clear: 'radial-gradient(circle at 50% -10%,#ffcc00,#ff9500 20%,#06090f 70%)', Clouds: 'linear-gradient(180deg,#3c3c3c,#111)' }; bg.style.background = colors[condition] || '#06090f'; if (['Rain', 'Drizzle', 'Thunderstorm'].includes(condition)) createParticles(c, 'rain', condition === 'Thunderstorm' ? 80 : 120); if (condition === 'Snow') createParticles(c, 'snow', 100); if (condition === 'Clear') createParticles(c, 'star', 30); if (condition === 'Thunderstorm') lightningInterval = setInterval(() => { if (Math.random() > .93) { bg.style.filter = 'brightness(4) saturate(2)'; setTimeout(() => bg.style.filter = 'none', 200) } }, 1000) } function createParticles(c, type, count) { for (let i = 0; i < count; i++) { const p = document.createElement('div'); p.style.cssText = `position:absolute;left:${Math.random() * 100}%;top:-5%;pointer-events:none;`; if (type === 'rain') { p.style.cssText += 'width:1px;height:25px;background:rgba(255,255,255,.3);'; p.animate([{ transform: 'translateY(0)' }, { transform: 'translateY(105vh)' }], { duration: 500 + Math.random() * 1000, iterations: Infinity }) } else if (type === 'snow') { const s = 2 + Math.random() * 6; p.style.cssText += `width:${s}px;height:${s}px;background:#fff;border-radius:50%;`; p.animate([{ transform: 'translate(0,0)' }, { transform: `translate(${Math.random() * 100 - 50}px,105vh)` }], { duration: 3000 + Math.random() * 3000, iterations: Infinity }) } else { p.style.cssText += 'top:' + Math.random() * 100 + '%;width:2px;height:2px;background:#fff;'; p.animate([{ opacity: .2 }, { opacity: 1 }, { opacity: .2 }], { duration: 2000, iterations: Infinity }) } c.appendChild(p) } } async function fetchWeather(city = '') { try { const q = city || DEFAULT_CITY; if (!q) return; const [a, b] = await Promise.all([fetch(`https://api.openweathermap.org/data/2.5/weather?q=${q}&units=metric&lang=fr&appid=${API_KEY}`), fetch(`https://api.openweathermap.org/data/2.5/forecast?q=${q}&units=metric&lang=fr&appid=${API_KEY}`)]); const d = await a.json(), f = await b.json(); if (d.cod === 200) { document.getElementById('city-name').innerText = d.name; document.getElementById('main-temp').innerText = Math.round(d.main.temp); document.getElementById('weather-desc').innerText = d.weather[0].description; document.getElementById('weather-icon').src = `https://openweathermap.org/img/wn/${d.weather[0].icon}@4x.png`; document.getElementById('val-wind').innerText = Math.round(d.wind.speed * 3.6); document.getElementById('val-hum').innerText = d.main.humidity; const badge = document.getElementById('risk-badge'), danger = d.weather[0].main === 'Thunderstorm' || d.wind.speed > 15; badge.innerText = danger ? 'ALERTE MÉTÉO' : 'SITUATION CALME'; badge.className = `risk-badge ${danger ? 'risk-high' : 'risk-low'}`; map.flyTo([d.coord.lat, d.coord.lon], 10); updateDynamicBackground(d.weather[0].main); document.getElementById('forecast-container').innerHTML = f.list.filter(x => x.dt_txt.includes('12:00:00')).map(x => `<div class="forecast-day"><div style="font-weight:800">${new Date(x.dt * 1000).toLocaleDateString('fr-FR', { weekday: 'short' }).toUpperCase()}</div><img src="https://openweathermap.org/img/wn/${x.weather[0].icon}.png" style="width:40px"><div style="color:var(--accent);font-weight:bold">${Math.round(x.main.temp)}°</div></div>`).join('') } } catch (e) { console.error('Erreur météo:', e) } } document.getElementById('city-search').addEventListener('keypress', e => { if (e.key === 'Enter') fetchWeather(e.target.value) });</script>