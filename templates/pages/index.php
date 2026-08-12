<?php if ($page->getId() != 5): ?>
  <div class="container-md-fluid content-section-page py-5" id="contenu-page">
    <div class="container-md content">
      <?php if ($page->getId() == 8): ?>

        <style>
          /* ==========================================
   VARIABLES DE COULEURS AVVA 39 & GLASSMORPHISM
   ========================================== */
:root {
  /* Couleurs AVVA 39 */
  --avva-navy: #002B49;          /* Bleu marine écusson */
  --avva-sky: #134074;           /* Bleu aéro */
  --avva-accent: #FFC72C;        /* Jaune/Or soleil et planeur */
  
  /* System & UI */
  --primary: var(--avva-navy);
  --primary-glow: rgba(0, 43, 73, 0.12);
  --accent: var(--avva-sky);
  --accent-color: #0d6efd;
  --success-color: #198754;
  --danger-color: #dc3545;
  
  /* Glassmorphism */
  --glass-bg: rgba(255, 255, 255, 0.65);
  --glass-border: rgba(255, 255, 255, 0.4);
  --glass: rgba(255, 255, 255, 0.8);
  --glass-heavy: rgba(255, 255, 255, 0.95);
  --glass-shadow: 0 20px 40px rgba(0, 43, 73, 0.06);
  
  /* Typographie & Bordures */
  --border: rgba(0, 0, 0, 0.08);
  --text-main: #1e293b;
  --text-dark: #0f172a;

  /* Transitions standardisées */
  --transition-fast: 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  --transition-normal: 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

/* ==========================================
   ANIMATIONS & KEYFRAMES
   ========================================== */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(24px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-24px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-8px); }
}

@keyframes floatLeft {
  0%, 100% { transform: translate(0, 0) rotate(-4deg); }
  50% { transform: translate(-8px, -8px) rotate(4deg); }
}

@keyframes floatRight {
  0%, 100% { transform: translate(0, 0) rotate(4deg); }
  50% { transform: translate(8px, -8px) rotate(-4deg); }
}

@keyframes shine {
  to { background-position: 200% center; }
}

@keyframes scaleWidth {
  to { transform: scaleX(1); }
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

/* Classes utilitaires d'animation */
.reveal {
  animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  opacity: 0;
}
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }
.delay-3 { animation-delay: 0.3s; }

/* ==========================================
   EN-TÊTES (HERO & HEADER PREMIUM)
   ========================================== */
.instruction-wrapper {
  background: var(--glass-bg);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid var(--glass-border);
  border-radius: 24px;
  padding: 2.5rem;
  box-shadow: var(--glass-shadow);
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
  transition: box-shadow var(--transition-normal);
}

.instruction-wrapper:hover {
  box-shadow: 0 25px 50px rgba(0, 43, 73, 0.1);
}

.hero-header {
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  align-items: center;
  gap: 20px;
  padding: 40px 15px;
  max-width: 1200px;
  margin: 0 auto;
}

.club-logo {
  max-width: 120px;
  width: 100%;
  height: auto;
  margin-bottom: 20px;
  filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.08));
  animation: float 5s ease-in-out infinite;
  transition: transform var(--transition-normal);
}

.club-logo:hover {
  transform: scale(1.05);
}

.display-title {
  font-weight: 800;
  font-size: clamp(1.5rem, 5vw, 2.5rem);
  color: var(--text-dark);
  margin: 15px 0 5px;
  letter-spacing: -0.02em;
}

.brand-tag {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  color: #64748b;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 2px;
  font-weight: 600;
}

.brand-tag .line {
  flex: 1;
  height: 1px;
  background: linear-gradient(to right, transparent, #cbd5e1, transparent);
  max-width: 50px;
}

.floating-icon {
  max-width: 80px;
  width: 100%;
  height: auto;
  border-radius: 16px;
  transition: transform var(--transition-normal), box-shadow var(--transition-normal);
}

.floating-icon:hover {
  transform: scale(1.1) translateY(-4px) !important;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}

.left { animation: floatLeft 6s infinite ease-in-out; }
.right { animation: floatRight 6s infinite ease-in-out; }

.header-premium {
  display: flex;
  justify-content: center;
  align-items: center;
  perspective: 1000px;
}

.header-content {
  text-align: center;
  max-width: 900px;
}

.badge-header {
  display: inline-block;
  padding: 6px 18px;
  border-radius: 50px;
  background: var(--primary-glow);
  color: var(--avva-navy);
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 24px;
  border: 1px solid rgba(0, 43, 73, 0.15);
  animation: fadeInDown 0.8s ease-out forwards;
  transition: transform var(--transition-fast), background-color var(--transition-fast);
}

.badge-header:hover {
  transform: translateY(-2px);
  background: rgba(0, 43, 73, 0.2);
}

.main-title {
  font-size: clamp(2.2rem, 8vw, 5rem);
  font-weight: 800;
  letter-spacing: -0.04em;
  line-height: 1.1;
  color: var(--avva-navy);
  margin: 0;
  background: linear-gradient(120deg, var(--avva-navy) 20%, var(--avva-sky) 50%, var(--avva-navy) 80%);
  background-size: 200% auto;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: shine 6s linear infinite, fadeInUp 1s ease-out forwards;
}

.main-title span {
  display: block;
  filter: drop-shadow(0 4px 12px rgba(19, 64, 116, 0.15));
}

.divider {
  width: 60px;
  height: 4px;
  background: var(--avva-accent);
  margin: 30px auto;
  border-radius: 10px;
  animation: scaleWidth 1s ease-out forwards 0.5s;
  transform: scaleX(0);
}

.subtitle {
  font-size: clamp(1rem, 3vw, 1.25rem);
  color: var(--text-main);
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
  opacity: 0;
  animation: fadeInUp 1s ease-out forwards 0.8s;
}

/* ==========================================
   LAYOUT BENTO (MEMBERSHIP & SIDEBAR)
   ========================================== */
.membership-container {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 2.5rem;
  align-items: start;
  position: relative;
  width: 100%;
}

.content-area {
	flex: 1;
	position: -webkit-sticky;
	position: sticky;
	top: 2rem;
	z-index: 1000;
	display: flex;
	flex-direction: column;
	gap: 1.5rem;
	max-height: calc(100vh - 4rem);
	overflow-y: auto;
	/* scrollbar-width: none; */
}

.sticky-sidebar {
  width: 100%;
  position: -webkit-sticky;
  position: sticky;
  top: 2rem;
  z-index: 100;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  max-height: calc(100vh - 4rem);
  overflow-y: auto;
  scrollbar-width: none;
}

.sticky-sidebar::-webkit-scrollbar {
  display: none;
}

.sticky-sidebar a {
  transition: color var(--transition-fast), transform var(--transition-fast);
}

.sticky-sidebar a:hover {
  color: var(--avva-sky);
  transform: translateX(4px);
}

/* ==========================================
   CARDS & BOUTONS (CHARTE AVVA 39)
   ========================================== */
.action-card {
  background: var(--glass);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid var(--border);
  border-radius: 24px;
  padding: 1.5rem;
  box-shadow: var(--glass-shadow);
  transition: all var(--transition-normal);
}

.action-card:hover {
  border-color: rgba(19, 64, 116, 0.3);
  transform: translateY(-4px);
  box-shadow: 0 25px 50px rgba(19, 64, 116, 0.08);
}

.step-container {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.step-item {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  padding: 1rem 1.25rem;
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid var(--border);
  transition: all var(--transition-normal);
}

.step-item:hover {
  transform: translateX(6px);
  border-color: var(--avva-navy);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
}

.step-badge {
  width: 36px;
  height: 36px;
  min-width: 36px;
  background: var(--avva-navy);
  color: #ffffff;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  transition: transform var(--transition-fast);
}

.step-item:hover .step-badge {
  transform: scale(1.08);
}

.step-number {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, var(--avva-navy), var(--avva-sky));
  color: #ffffff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 15px;
  font-weight: 800;
  box-shadow: 0 6px 16px rgba(0, 43, 73, 0.25);
  transition: transform var(--transition-normal);
}

.step-number:hover {
  transform: scale(1.1) rotate(5deg);
}

.btn-premium {
  position: relative;
  overflow: hidden;
  background: var(--avva-navy);
  color: #ffffff !important;
  border: none;
  padding: 1rem 2rem;
  border-radius: 16px;
  font-weight: 700;
  width: 100%;
  display: inline-block;
  text-align: center;
  transition: all var(--transition-normal);
  box-shadow: 0 4px 14px rgba(0, 43, 73, 0.2);
}

.btn-premium::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
  animation: shimmer 2.5s infinite;
}

.btn-premium:hover {
  background: var(--avva-sky);
  transform: translateY(-3px);
  box-shadow: 0 8px 22px rgba(19, 64, 116, 0.3);
}

.btn-premium:active {
  transform: translateY(-1px);
}

.download-card {
  background: rgba(255, 255, 255, 0.6) !important;
  border: 1px solid var(--glass-border) !important;
  backdrop-filter: blur(8px);
  border-radius: 20px !important;
  transition: all var(--transition-normal);
}

.download-card:hover {
  transform: translateY(-4px);
  background: rgba(255, 255, 255, 0.95) !important;
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06) !important;
}

.btn-download {
  background: linear-gradient(135deg, var(--avva-navy), var(--avva-sky));
  border: none;
  border-radius: 50px;
  font-weight: 600;
  color: #ffffff;
  padding: 0.75rem 1.5rem;
  transition: all var(--transition-fast);
}

.btn-download:hover {
  transform: scale(1.03);
  box-shadow: 0 6px 18px rgba(0, 43, 73, 0.25);
}

/* ==========================================
   TABLEAUX & SECTIONS LÉGALES
   ========================================== */
.table-responsive-wrapper {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.insurance-section {
  background: rgba(255, 255, 255, 0.75);
  border-radius: 20px;
  padding: 24px;
  margin-top: 30px;
  border: 1px solid var(--glass-border);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.02);
}

.insurance-table {
  width: 100%;
  font-size: 0.875rem;
  margin-top: 15px;
  border-collapse: separate;
  border-spacing: 0;
}

.insurance-table th {
  background: #f8fafc;
  padding: 14px;
  border-bottom: 2px solid #e2e8f0;
  color: var(--text-main);
  font-weight: 700;
}

.insurance-table td {
  padding: 12px 14px;
  border-bottom: 1px solid #f1f5f9;
}

.insurance-table tr:hover td {
  background-color: rgba(248, 250, 252, 0.8);
}

.table-braquet {
  width: 100%;
  border-collapse: separate !important;
  border-spacing: 0 10px !important;
  background-color: transparent !important;
}

.table-braquet thead th {
  background-color: var(--avva-navy);
  color: #ffffff;
  border: none;
  padding: 16px 20px;
  position: sticky;
  top: 0;
  z-index: 10;
  font-weight: 700;
}

.table-braquet thead th:first-child { border-radius: 12px 0 0 12px; }
.table-braquet thead th:last-child { border-radius: 0 12px 12px 0; }

.table-braquet tbody tr {
  transition: transform var(--transition-fast);
  background-color: transparent !important;
}

.table-braquet tbody tr:hover {
  transform: translateY(-2px);
}

.table-braquet tbody tr:hover td {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
  background-color: #ffffff !important;
}

.table-braquet td {
  padding: 16px 20px !important;
  border: none !important;
  vertical-align: middle;
  background-color: #ffffff;
  border-top: 1px solid #f1f5f9;
  border-bottom: 1px solid #f1f5f9;
  transition: background-color var(--transition-fast), box-shadow var(--transition-fast);
}

.table-braquet td:first-child {
  border-radius: 14px 0 0 14px !important;
  border-left: 1px solid #f1f5f9;
}

.table-braquet td:last-child {
  border-radius: 0 14px 14px 0 !important;
  border-right: 1px solid #f1f5f9;
}

.container-tableau {
  display: flex;
  align-items: stretch;
  gap: 24px;
}

.colonne {
  flex: 1;
  padding: 10px;
}

.separation-gauche {
  border-left: 1px solid #e2e8f0;
  padding-left: 24px;
}

.legal-header {
  border-bottom: 4px solid var(--avva-navy);
  margin-bottom: 2rem;
  padding-bottom: 0.5rem;
}

.section-title {
  background-color: var(--avva-navy);
  color: #ffffff;
  padding: 12px 18px;
  text-transform: uppercase;
  margin-top: 2rem;
  border-radius: 12px;
  letter-spacing: 0.5px;
  font-weight: 700;
}

.sub-section-title {
  color: #c53030;
  border-bottom: 2px solid #c53030;
  margin-top: 1.5rem;
  padding-bottom: 6px;
  font-weight: 700;
}

.exclusion-card {
  border-left: 4px solid var(--danger-color);
  background-color: #fff5f5;
  border-radius: 0 12px 12px 0;
  padding: 1.25rem;
}

/* ==========================================
   CANVAS & VIEWERS PDF
   ========================================== */
.pdf-viewer-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 30px 10px;
  width: 100%;
}

canvas, .pdf-page-canvas {
  display: block;
  width: 100% !important;
  max-width: 100%;
  height: auto !important;
  margin: 0 auto 30px auto;
  background: var(--glass-bg);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.8s ease, transform 0.8s ease, box-shadow var(--transition-normal);
}

canvas.loaded {
  opacity: 1;
  transform: translateY(0);
}

@media (hover: hover) {
  canvas:hover, .pdf-page-canvas:hover {
    transform: translateY(-4px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.12);
  }
}

/* ==========================================
   UTILITAIRES, BADGES & ACCORDÉON
   ========================================== */
.notice-box {
  background: rgba(0, 43, 73, 0.03);
  border-left: 4px solid var(--avva-navy);
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  text-align: left;
}

.badge-acquis {
  background-color: #d1e7dd;
  color: #0f5132;
  border: 1px solid #badbcc;
}

.badge-non-acquis {
  background-color: #f8d7da;
  color: #842029;
  border: 1px solid #f5c2c7;
}

.badge {
  padding: 6px 12px;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.65rem;
  letter-spacing: 0.5px;
  border-radius: 6px;
}

.text-highlight {
  color: var(--avva-navy);
  font-weight: 700;
}

.lexique-term {
  font-weight: 700;
  color: var(--avva-navy);
}

.lexique-horizontal-card {
  background: #ffffff;
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  transition: all var(--transition-normal);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
}

.lexique-horizontal-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
  border-color: var(--avva-sky);
}

.icon-box-md {
  width: 50px;
  height: 50px;
  min-width: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  font-size: 1.5rem;
}

.modern-accordion .accordion-item {
  border: 1px solid var(--border);
  border-radius: 14px !important;
  overflow: hidden;
  margin-bottom: 12px;
  transition: border-color var(--transition-fast);
}

.modern-accordion .accordion-item:hover {
  border-color: rgba(19, 64, 116, 0.3);
}

.modern-accordion .accordion-button {
  background-color: #ffffff;
  color: var(--text-dark);
  padding: 1.25rem;
  font-weight: 600;
  box-shadow: none !important;
}

.modern-accordion .accordion-button:not(.collapsed) {
  background-color: #f8fbff;
  color: var(--avva-navy);
}

#progress-bar {
  position: fixed;
  top: 12px;
  left: 50%;
  transform: translateX(-50%);
  width: min(92%, 1200px);
  height: 6px;
  background: linear-gradient(90deg, var(--avva-accent), var(--avva-sky));
  border-radius: 100px;
  z-index: 1000;
  box-shadow: 0 4px 12px rgba(0, 43, 73, 0.15);
  transition: width 0.15s ease-out;
}

@media (max-width: 768px) {
  #progress-bar {
    top: 8px;
    width: calc(100% - 24px);
    height: 5px;
  }
}

.loading-screen {
  position: fixed;
  inset: 0;
  background: #ffffff;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  transition: opacity 0.5s ease;
}

.copy-chip {
  background: #f1f5f9;
  padding: 8px 16px;
  border-radius: 100px;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all var(--transition-fast);
}

.copy-chip:hover {
  background: var(--primary-glow);
  color: var(--avva-navy);
  transform: scale(1.02);
}

.copy-chip:active {
  transform: scale(0.98);
}

.copy-tooltip {
  position: absolute;
  top: -40px;
  left: 50%;
  transform: translateX(-50%);
  background: var(--text-dark);
  color: #ffffff;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
  display: none;
  z-index: 10;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* ==========================================
   ADAPTATIONS RESPONSIVES (MOBILE / TABLETTE)
   ========================================== */
@media (max-width: 1100px) {
  .membership-container {
    grid-template-columns: 1fr;
  }

  .content-area,
  .sticky-sidebar {
    position: relative !important;
    top: 0 !important;
    max-height: none;
    width: 100%;
  }
}

@media (max-width: 768px) {
  .instruction-wrapper {
    padding: 1.5rem 1.25rem;
    border-radius: 20px;
  }

  .hero-header {
    grid-template-columns: 1fr;
    grid-template-areas:
      "logo"
      "title"
      "icons";
    text-align: center;
    padding: 20px 10px;
  }

  .side-decoration {
    display: flex;
    justify-content: center;
    gap: 20px;
    grid-area: icons;
  }

  .side-decoration.right {
    margin-left: 0;
  }

  .floating-icon {
    max-width: 60px;
  }

  .container-tableau {
    flex-direction: column;
    gap: 12px;
  }

  .separation-gauche {
    border-left: none;
    border-top: 1px solid #e2e8f0;
    padding-left: 0;
    padding-top: 16px;
  }

  .table-braquet td, 
  .table-braquet thead th {
    padding: 12px 10px !important;
    font-size: 0.825rem;
  }
}

/* ==========================================
   ACCESSIBILITÉ (PRÉFÉRENCE DE MOUVEMENT)
   ========================================== */
@media (prefers-reduced-motion: reduce) {
  *, ::before, ::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
        </style>

        <div class="container-md mt-5 mb-5">
          <div class="instruction-wrapper">

            <div class="glass-card p-4 animate__animated animate__fadeIn">

              <div class="hero-header">
                <div class="side-decoration left">
                  <img src="/uploads/page-comment-adherer/images/image1.png" alt="" class="floating-icon">
                </div>

                <div class="main-content text-center">
                  <div class="logo-wrapper">
                    <img src="/assets/images/logo-avva39.png" alt="AVVA39" class="club-logo">
                  </div>
                  <h3 class="display-title">Adhésion Saison 2026 AVVA39</h3>
                  <div class="brand-tag">
                    <span class="line"></span>
                    <p>Amicale Vélo du Val d'Amour</p>
                    <span class="line"></span>
                  </div>
                </div>

                <div class="side-decoration right">
                  <img src="/uploads/page-comment-adherer/images/image2.png" alt="" class="floating-icon">
                </div>
              </div>

            </div>

            <div class="membership-container">

              <div class="content-area">
                <?php foreach ($fichiersPdf as $fichierPdf): ?>
                  <?php if ($fichierPdf->getEstAfficher() == 1): ?>
                    <div id="progress-bar"></div>
                    <div id="loader" class="loading-screen">Déchiffrement du document...</div>
                    <header class="header-premium">
                      <div class="header-content">
                        <span class="badge-header"><?= $fichierPdf->getThematique() ?></span>
                        <h1 class="main-title">
                          <?= $fichierPdf->getNom() ?>
                        </h1>
                        <div class="divider"></div>
                        <p class="subtitle">
                          <?= $fichierPdf->getDescription() ?>
                        </p>
                      </div>
                    </header>
                    <div id="viewer-container-<?= $fichierPdf->getId() ?>" class="reveal delay-2"></div>

                    <script type="module">
                      import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.mjs';

                      // Configuration du worker indispensable
                      pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.worker.mjs';

                      const url<?= $fichierPdf->getId() ?> = '/<?= $fichierPdf->getFichier() ?>'; // METTEZ VOTRE LIEN ICI
                      const container<?= $fichierPdf->getId() ?> = document.getElementById('viewer-container-<?= $fichierPdf->getId() ?>');
                      const progressBar = document.getElementById('progress-bar');

                      async function initPDF<?= $fichierPdf->getId() ?>() {
                        try {
                          const loadingTask = pdfjsLib.getDocument(url<?= $fichierPdf->getId() ?>);
                          const pdf = await loadingTask.promise;

                          document.getElementById('loader').style.opacity = '0';
                          setTimeout(() => document.getElementById('loader').remove(), 500);

                          for (let i = 1; i <= pdf.numPages; i++) {
                            await renderPage<?= $fichierPdf->getId() ?>(pdf, i);
                            // Mise à jour de la barre de progression
                            progressBar.style.width = `${(i / pdf.numPages) * 97}%`;
                          }
                        } catch (error) {
                          console.error("Erreur critique:", error);
                          document.getElementById('loader').innerText = "Fichier introuvable ou erreur de sécurité.";
                        }
                      }

                      async function renderPage<?= $fichierPdf->getId() ?>(pdf, num) {
                        const page = await pdf.getPage(num);
                        const viewport = page.getViewport({ scale: 2 }); // Qualité Retina

                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        container<?= $fichierPdf->getId() ?>.appendChild(canvas);

                        await page.render({ canvasContext: context, viewport: viewport }).promise;

                        // Déclenche l'animation
                        requestAnimationFrame(() => canvas.classList.add('loaded'));
                      }

                      initPDF<?= $fichierPdf->getId() ?>();
                    </script>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>

              <div class="sticky-sidebar">
                <div class="notice-box shadow-sm action-card reveal delay-2">
                  <h6 class="fw-bold text-primary mb-2"><i class="fa-solid fa-circle-info me-2"></i> VOTRE DOSSIER
                    D'INSCRIPTION
                  </h6>
                  <ul class="small text-muted mb-0 ps-3">
                    <li><strong>Documents obligatoires :</strong> Bulletin d'adhésion + Formulaire droit à l'image.</li>
                    <li><strong>Mineurs :</strong> Ajouter l'Autorisation parentale + Questionnaire santé jeune.</li>
                    <li><strong>Assurance :</strong> Consultez la notice d'information FFVELO avant de signer.
                    </li>
                  </ul>
                </div>

                <div class="action-card reveal delay-2">
                  <h5 class="fw-bold mb-4 text-dark d-flex align-items-center">
                    <span class="p-2 bg-primary-glow rounded-3 me-2 text-primary"><i class="fa-solid fa-bolt"></i></span>
                    Comment rejoindre ?
                  </h5>
                  <div class="step-container">
                    <div class="step-item">
                      <div class="step-badge">1</div>
                      <span class="small text-muted fw-bold">Prenez connaissance des documents à gauche.</span>
                    </div>
                    <div class="step-item">
                      <div class="step-badge">2</div>
                      <span class="small text-muted fw-bold">Téléchargez et remplissez le bulletin officiel.</span>
                    </div>
                    <div class="step-item">
                      <div class="step-badge">3</div>
                      <span class="small text-muted fw-bold">Signez et renvoyez le dossier complet.</span>
                    </div>
                  </div>
                </div>

                <div class="action-card reveal delay-3">
                  <h6 class="fw-800 mb-3 text-muted small text-uppercase ls-1">Fichiers à télécharger</h6>
                  <div class="d-grid gap-2">
                    <?php foreach ($fichiersPdf as $fichierPdf): ?>
                      <?php if ($fichierPdf->getEstTelechargeable() == 1): ?>
                        <a href="/<?= $fichierPdf->getFichier() ?>" download
                          class="btn btn-white text-start border d-flex justify-content-between align-items-center p-3 rounded-4 shadow-sm hover-scale">
                          <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <div class="p-2 bg-light rounded-3 text-danger"><i class="fa-solid fa-file-pdf"></i></div>
                            <span class="text-truncate small fw-800"><?= $fichierPdf->getNom() ?></span>
                          </div>
                          <i class="fa-solid fa-chevron-right text-muted small"></i>
                        </a>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                </div>

                <div class="text-center pt-5 mt-4 border-top border-light">
                  <p class="mb-3 text-muted small fw-bold text-uppercase">Envoyez votre dossier à la secrétaire :</p>
                  <div class="email-container shadow-sm p-2">
                    <a href="mailto:secretaire-avva39@outlook.fr?subject=Adhésion%20Club%20AVVA39%202026"
                      class="btn btn-primary rounded-pill px-4 fw-bold me-2">
                      <i class="fa-solid fa-paper-plane me-2"></i>secretaire-avva39@outlook.fr
                    </a>
                    <button class="btn btn-light rounded-pill border py-2 px-3" onclick="copyEmail()" id="copyBtn"
                      title="Copier l'adresse">
                      <i class="fa-regular fa-copy"></i>
                      <span class="copy-tooltip" id="tooltip">Copié !</span>
                    </button>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <script>
            function copyEmail() {
              const email = "secretaire-avva39@outlook.fr";
              navigator.clipboard.writeText(email).then(() => {
                const tooltip = document.getElementById("tooltip");
                tooltip.style.opacity = "1";
                setTimeout(() => { tooltip.style.opacity = "0"; }, 2000);
              });
            }
          </script>
        <?php endif; ?>
        <?php if ($page->getId() != 7): ?>
          <?= $page->getContenu() ?>
        <?php else: ?>

        <?php endif; ?>
        <?php if ($page->getId() != 8): ?>
          <style>
            /* ==========================================
   VARIABLES DE COULEURS AVVA 39 & GLASSMORPHISM
   ========================================== */
:root {
  /* Couleurs AVVA 39 */
  --avva-navy: #002B49;          /* Bleu marine écusson */
  --avva-sky: #134074;           /* Bleu aéro */
  --avva-accent: #FFC72C;        /* Jaune/Or soleil et planeur */
  
  /* System & UI */
  --primary: var(--avva-navy);
  --primary-glow: rgba(0, 43, 73, 0.12);
  --accent: var(--avva-sky);
  --accent-color: #0d6efd;
  --success-color: #198754;
  --danger-color: #dc3545;
  
  /* Glassmorphism */
  --glass-bg: rgba(255, 255, 255, 0.65);
  --glass-border: rgba(255, 255, 255, 0.4);
  --glass: rgba(255, 255, 255, 0.8);
  --glass-heavy: rgba(255, 255, 255, 0.95);
  --glass-shadow: 0 20px 40px rgba(0, 43, 73, 0.06);
  
  /* Typographie & Bordures */
  --border: rgba(0, 0, 0, 0.08);
  --text-main: #1e293b;
  --text-dark: #0f172a;

  /* Transitions standardisées */
  --transition-fast: 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  --transition-normal: 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

/* ==========================================
   ANIMATIONS & KEYFRAMES
   ========================================== */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(24px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-24px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-8px); }
}

@keyframes floatLeft {
  0%, 100% { transform: translate(0, 0) rotate(-4deg); }
  50% { transform: translate(-8px, -8px) rotate(4deg); }
}

@keyframes floatRight {
  0%, 100% { transform: translate(0, 0) rotate(4deg); }
  50% { transform: translate(8px, -8px) rotate(-4deg); }
}

@keyframes shine {
  to { background-position: 200% center; }
}

@keyframes scaleWidth {
  to { transform: scaleX(1); }
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

/* Classes utilitaires d'animation */
.reveal {
  animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  opacity: 0;
}
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }
.delay-3 { animation-delay: 0.3s; }

/* ==========================================
   EN-TÊTES (HERO & HEADER PREMIUM)
   ========================================== */
.instruction-wrapper {
  background: var(--glass-bg);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid var(--glass-border);
  border-radius: 24px;
  padding: 2.5rem;
  box-shadow: var(--glass-shadow);
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
  transition: box-shadow var(--transition-normal);
}

.instruction-wrapper:hover {
  box-shadow: 0 25px 50px rgba(0, 43, 73, 0.1);
}

.hero-header {
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  align-items: center;
  gap: 20px;
  padding: 40px 15px;
  max-width: 1200px;
  margin: 0 auto;
}

.club-logo {
  max-width: 120px;
  width: 100%;
  height: auto;
  margin-bottom: 20px;
  filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.08));
  animation: float 5s ease-in-out infinite;
  transition: transform var(--transition-normal);
}

.club-logo:hover {
  transform: scale(1.05);
}

.display-title {
  font-weight: 800;
  font-size: clamp(1.5rem, 5vw, 2.5rem);
  color: var(--text-dark);
  margin: 15px 0 5px;
  letter-spacing: -0.02em;
}

.brand-tag {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  color: #64748b;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 2px;
  font-weight: 600;
}

.brand-tag .line {
  flex: 1;
  height: 1px;
  background: linear-gradient(to right, transparent, #cbd5e1, transparent);
  max-width: 50px;
}

.floating-icon {
  max-width: 80px;
  width: 100%;
  height: auto;
  border-radius: 16px;
  transition: transform var(--transition-normal), box-shadow var(--transition-normal);
}

.floating-icon:hover {
  transform: scale(1.1) translateY(-4px) !important;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}

.left { animation: floatLeft 6s infinite ease-in-out; }
.right { animation: floatRight 6s infinite ease-in-out; }

.header-premium {
  display: flex;
  justify-content: center;
  align-items: center;
  perspective: 1000px;
}

.header-content {
  text-align: center;
  max-width: 900px;
}

.badge-header {
  display: inline-block;
  padding: 6px 18px;
  border-radius: 50px;
  background: var(--primary-glow);
  color: var(--avva-navy);
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 24px;
  border: 1px solid rgba(0, 43, 73, 0.15);
  animation: fadeInDown 0.8s ease-out forwards;
  transition: transform var(--transition-fast), background-color var(--transition-fast);
}

.badge-header:hover {
  transform: translateY(-2px);
  background: rgba(0, 43, 73, 0.2);
}

.main-title {
  font-size: clamp(2.2rem, 8vw, 5rem);
  font-weight: 800;
  letter-spacing: -0.04em;
  line-height: 1.1;
  color: var(--avva-navy);
  margin: 0;
  background: linear-gradient(120deg, var(--avva-navy) 20%, var(--avva-sky) 50%, var(--avva-navy) 80%);
  background-size: 200% auto;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: shine 6s linear infinite, fadeInUp 1s ease-out forwards;
}

.main-title span {
  display: block;
  filter: drop-shadow(0 4px 12px rgba(19, 64, 116, 0.15));
}

.divider {
  width: 60px;
  height: 4px;
  background: var(--avva-accent);
  margin: 30px auto;
  border-radius: 10px;
  animation: scaleWidth 1s ease-out forwards 0.5s;
  transform: scaleX(0);
}

.subtitle {
  font-size: clamp(1rem, 3vw, 1.25rem);
  color: var(--text-main);
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
  opacity: 0;
  animation: fadeInUp 1s ease-out forwards 0.8s;
}

/* ==========================================
   LAYOUT BENTO (MEMBERSHIP & SIDEBAR)
   ========================================== */
.membership-container {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 2.5rem;
  align-items: start;
  position: relative;
  width: 100%;
}

.content-area {
	flex: 1;
	position: -webkit-sticky;
	position: sticky;
	top: 2rem;
	z-index: 1000;
	display: flex;
	flex-direction: column;
	gap: 1.5rem;
	max-height: calc(100vh - 4rem);
	overflow-y: auto;
	/* scrollbar-width: none; */
}

.sticky-sidebar {
  width: 100%;
  position: -webkit-sticky;
  position: sticky;
  top: 2rem;
  z-index: 100;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  max-height: calc(100vh - 4rem);
  overflow-y: auto;
  scrollbar-width: none;
}

.sticky-sidebar::-webkit-scrollbar {
  display: none;
}

.sticky-sidebar a {
  transition: color var(--transition-fast), transform var(--transition-fast);
}

.sticky-sidebar a:hover {
  color: var(--avva-sky);
  transform: translateX(4px);
}

/* ==========================================
   CARDS & BOUTONS (CHARTE AVVA 39)
   ========================================== */
.action-card {
  background: var(--glass);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid var(--border);
  border-radius: 24px;
  padding: 1.5rem;
  box-shadow: var(--glass-shadow);
  transition: all var(--transition-normal);
}

.action-card:hover {
  border-color: rgba(19, 64, 116, 0.3);
  transform: translateY(-4px);
  box-shadow: 0 25px 50px rgba(19, 64, 116, 0.08);
}

.step-container {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.step-item {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  padding: 1rem 1.25rem;
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid var(--border);
  transition: all var(--transition-normal);
}

.step-item:hover {
  transform: translateX(6px);
  border-color: var(--avva-navy);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
}

.step-badge {
  width: 36px;
  height: 36px;
  min-width: 36px;
  background: var(--avva-navy);
  color: #ffffff;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  transition: transform var(--transition-fast);
}

.step-item:hover .step-badge {
  transform: scale(1.08);
}

.step-number {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, var(--avva-navy), var(--avva-sky));
  color: #ffffff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 15px;
  font-weight: 800;
  box-shadow: 0 6px 16px rgba(0, 43, 73, 0.25);
  transition: transform var(--transition-normal);
}

.step-number:hover {
  transform: scale(1.1) rotate(5deg);
}

.btn-premium {
  position: relative;
  overflow: hidden;
  background: var(--avva-navy);
  color: #ffffff !important;
  border: none;
  padding: 1rem 2rem;
  border-radius: 16px;
  font-weight: 700;
  width: 100%;
  display: inline-block;
  text-align: center;
  transition: all var(--transition-normal);
  box-shadow: 0 4px 14px rgba(0, 43, 73, 0.2);
}

.btn-premium::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
  animation: shimmer 2.5s infinite;
}

.btn-premium:hover {
  background: var(--avva-sky);
  transform: translateY(-3px);
  box-shadow: 0 8px 22px rgba(19, 64, 116, 0.3);
}

.btn-premium:active {
  transform: translateY(-1px);
}

.download-card {
  background: rgba(255, 255, 255, 0.6) !important;
  border: 1px solid var(--glass-border) !important;
  backdrop-filter: blur(8px);
  border-radius: 20px !important;
  transition: all var(--transition-normal);
}

.download-card:hover {
  transform: translateY(-4px);
  background: rgba(255, 255, 255, 0.95) !important;
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06) !important;
}

.btn-download {
  background: linear-gradient(135deg, var(--avva-navy), var(--avva-sky));
  border: none;
  border-radius: 50px;
  font-weight: 600;
  color: #ffffff;
  padding: 0.75rem 1.5rem;
  transition: all var(--transition-fast);
}

.btn-download:hover {
  transform: scale(1.03);
  box-shadow: 0 6px 18px rgba(0, 43, 73, 0.25);
}

/* ==========================================
   TABLEAUX & SECTIONS LÉGALES
   ========================================== */
.table-responsive-wrapper {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.insurance-section {
  background: rgba(255, 255, 255, 0.75);
  border-radius: 20px;
  padding: 24px;
  margin-top: 30px;
  border: 1px solid var(--glass-border);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.02);
}

.insurance-table {
  width: 100%;
  font-size: 0.875rem;
  margin-top: 15px;
  border-collapse: separate;
  border-spacing: 0;
}

.insurance-table th {
  background: #f8fafc;
  padding: 14px;
  border-bottom: 2px solid #e2e8f0;
  color: var(--text-main);
  font-weight: 700;
}

.insurance-table td {
  padding: 12px 14px;
  border-bottom: 1px solid #f1f5f9;
}

.insurance-table tr:hover td {
  background-color: rgba(248, 250, 252, 0.8);
}

.table-braquet {
  width: 100%;
  border-collapse: separate !important;
  border-spacing: 0 10px !important;
  background-color: transparent !important;
}

.table-braquet thead th {
  background-color: var(--avva-navy);
  color: #ffffff;
  border: none;
  padding: 16px 20px;
  position: sticky;
  top: 0;
  z-index: 10;
  font-weight: 700;
}

.table-braquet thead th:first-child { border-radius: 12px 0 0 12px; }
.table-braquet thead th:last-child { border-radius: 0 12px 12px 0; }

.table-braquet tbody tr {
  transition: transform var(--transition-fast);
  background-color: transparent !important;
}

.table-braquet tbody tr:hover {
  transform: translateY(-2px);
}

.table-braquet tbody tr:hover td {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
  background-color: #ffffff !important;
}

.table-braquet td {
  padding: 16px 20px !important;
  border: none !important;
  vertical-align: middle;
  background-color: #ffffff;
  border-top: 1px solid #f1f5f9;
  border-bottom: 1px solid #f1f5f9;
  transition: background-color var(--transition-fast), box-shadow var(--transition-fast);
}

.table-braquet td:first-child {
  border-radius: 14px 0 0 14px !important;
  border-left: 1px solid #f1f5f9;
}

.table-braquet td:last-child {
  border-radius: 0 14px 14px 0 !important;
  border-right: 1px solid #f1f5f9;
}

.container-tableau {
  display: flex;
  align-items: stretch;
  gap: 24px;
}

.colonne {
  flex: 1;
  padding: 10px;
}

.separation-gauche {
  border-left: 1px solid #e2e8f0;
  padding-left: 24px;
}

.legal-header {
  border-bottom: 4px solid var(--avva-navy);
  margin-bottom: 2rem;
  padding-bottom: 0.5rem;
}

.section-title {
  background-color: var(--avva-navy);
  color: #ffffff;
  padding: 12px 18px;
  text-transform: uppercase;
  margin-top: 2rem;
  border-radius: 12px;
  letter-spacing: 0.5px;
  font-weight: 700;
}

.sub-section-title {
  color: #c53030;
  border-bottom: 2px solid #c53030;
  margin-top: 1.5rem;
  padding-bottom: 6px;
  font-weight: 700;
}

.exclusion-card {
  border-left: 4px solid var(--danger-color);
  background-color: #fff5f5;
  border-radius: 0 12px 12px 0;
  padding: 1.25rem;
}

/* ==========================================
   CANVAS & VIEWERS PDF
   ========================================== */
.pdf-viewer-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 30px 10px;
  width: 100%;
}

canvas, .pdf-page-canvas {
  display: block;
  width: 100% !important;
  max-width: 100%;
  height: auto !important;
  margin: 0 auto 30px auto;
  background: var(--glass-bg);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.8s ease, transform 0.8s ease, box-shadow var(--transition-normal);
}

canvas.loaded {
  opacity: 1;
  transform: translateY(0);
}

@media (hover: hover) {
  canvas:hover, .pdf-page-canvas:hover {
    transform: translateY(-4px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.12);
  }
}

/* ==========================================
   UTILITAIRES, BADGES & ACCORDÉON
   ========================================== */
.notice-box {
  background: rgba(0, 43, 73, 0.03);
  border-left: 4px solid var(--avva-navy);
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  text-align: left;
}

.badge-acquis {
  background-color: #d1e7dd;
  color: #0f5132;
  border: 1px solid #badbcc;
}

.badge-non-acquis {
  background-color: #f8d7da;
  color: #842029;
  border: 1px solid #f5c2c7;
}

.badge {
  padding: 6px 12px;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.65rem;
  letter-spacing: 0.5px;
  border-radius: 6px;
}

.text-highlight {
  color: var(--avva-navy);
  font-weight: 700;
}

.lexique-term {
  font-weight: 700;
  color: var(--avva-navy);
}

.lexique-horizontal-card {
  background: #ffffff;
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  transition: all var(--transition-normal);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
}

.lexique-horizontal-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
  border-color: var(--avva-sky);
}

.icon-box-md {
  width: 50px;
  height: 50px;
  min-width: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  font-size: 1.5rem;
}

.modern-accordion .accordion-item {
  border: 1px solid var(--border);
  border-radius: 14px !important;
  overflow: hidden;
  margin-bottom: 12px;
  transition: border-color var(--transition-fast);
}

.modern-accordion .accordion-item:hover {
  border-color: rgba(19, 64, 116, 0.3);
}

.modern-accordion .accordion-button {
  background-color: #ffffff;
  color: var(--text-dark);
  padding: 1.25rem;
  font-weight: 600;
  box-shadow: none !important;
}

.modern-accordion .accordion-button:not(.collapsed) {
  background-color: #f8fbff;
  color: var(--avva-navy);
}

#progress-bar {
  position: fixed;
  top: 12px;
  left: 50%;
  transform: translateX(-50%);
  width: min(92%, 1200px);
  height: 6px;
  background: linear-gradient(90deg, var(--avva-accent), var(--avva-sky));
  border-radius: 100px;
  z-index: 1000;
  box-shadow: 0 4px 12px rgba(0, 43, 73, 0.15);
  transition: width 0.15s ease-out;
}

@media (max-width: 768px) {
  #progress-bar {
    top: 8px;
    width: calc(100% - 24px);
    height: 5px;
  }
}

.loading-screen {
  position: fixed;
  inset: 0;
  background: #ffffff;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  transition: opacity 0.5s ease;
}

.copy-chip {
  background: #f1f5f9;
  padding: 8px 16px;
  border-radius: 100px;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all var(--transition-fast);
}

.copy-chip:hover {
  background: var(--primary-glow);
  color: var(--avva-navy);
  transform: scale(1.02);
}

.copy-chip:active {
  transform: scale(0.98);
}

.copy-tooltip {
  position: absolute;
  top: -40px;
  left: 50%;
  transform: translateX(-50%);
  background: var(--text-dark);
  color: #ffffff;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
  display: none;
  z-index: 10;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* ==========================================
   ADAPTATIONS RESPONSIVES (MOBILE / TABLETTE)
   ========================================== */
@media (max-width: 1100px) {
  .membership-container {
    grid-template-columns: 1fr;
  }

  .content-area,
  .sticky-sidebar {
    position: relative !important;
    top: 0 !important;
    max-height: none;
    width: 100%;
  }
}

@media (max-width: 768px) {
  .instruction-wrapper {
    padding: 1.5rem 1.25rem;
    border-radius: 20px;
  }

  .hero-header {
    grid-template-columns: 1fr;
    grid-template-areas:
      "logo"
      "title"
      "icons";
    text-align: center;
    padding: 20px 10px;
  }

  .side-decoration {
    display: flex;
    justify-content: center;
    gap: 20px;
    grid-area: icons;
  }

  .side-decoration.right {
    margin-left: 0;
  }

  .floating-icon {
    max-width: 60px;
  }

  .container-tableau {
    flex-direction: column;
    gap: 12px;
  }

  .separation-gauche {
    border-left: none;
    border-top: 1px solid #e2e8f0;
    padding-left: 0;
    padding-top: 16px;
  }

  .table-braquet td, 
  .table-braquet thead th {
    padding: 12px 10px !important;
    font-size: 0.825rem;
  }
}

/* ==========================================
   ACCESSIBILITÉ (PRÉFÉRENCE DE MOUVEMENT)
   ========================================== */
@media (prefers-reduced-motion: reduce) {
  *, ::before, ::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
          </style>

          <div class="container-md mt-5 mb-5">
            <div class="instruction-wrapper">

              <div class="glass-card p-4 animate__animated animate__fadeIn">

                <div class="hero-header">
                  <div class="side-decoration left">
                    <img src="/<?= $page->getImageGauche() ?>" alt="" class="floating-icon">
                  </div>

                  <div class="main-content text-center">
                    <div class="logo-wrapper">
                      <img src="/assets/images/logo-avva39.png" alt="AVVA39" class="club-logo">
                    </div>
                    <h3 class="display-title"><?= $page->getNom() ?></h3>
                    <div class="brand-tag">
                      <span class="line"></span>
                      <p>Amicale Vélo du Val d'Amour</p>
                      <span class="line"></span>
                    </div>
                  </div>

                  <div class="side-decoration right">
                    <img src="/<?= $page->getImageDroite() ?>" alt="" class="floating-icon">
                  </div>
                </div>

              </div>

              <div class="membership-container">

                <div class="content-area">
                  <?php if ($page->getId() == 6): ?>
                    <div class="container mt-5 mb-5">
  <header class="header-premium">
    <div class="header-content">
      <h1 class="main-title">Calendrier</h1>
      <div class="divider"></div>
    </div>
  </header>

<button id="openMobileBtn" class="open-calendar-mobile-btn" aria-label="Ouvrir le calendrier">
  <div class="btn-left-content">
    <div class="calendar-badge">
      <div class="calendar-badge-header">
        <span class="pin"></span>
        <span class="pin"></span>
      </div>
      <div class="calendar-badge-body">
        <span id="mobileBtnDay" class="calendar-badge-day">--</span>
      </div>
    </div>

    <div class="btn-text-group">
      <span class="btn-title">Calendrier</span>
      <span class="btn-subtitle">Planification & Sorties</span>
    </div>
  </div>

  <div class="btn-arrow">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="9 18 15 12 9 6"></polyline>
    </svg>
  </div>
</button>

  <div class="calendar-toolbar">
    <button id="expandCalendarBtn" class="expand-btn" title="Agrandir le calendrier">
      <span class="expand-icon">⤢</span>
    </button>
  </div>

  <div id="calendarWrapper" class="calendar-container text-light p-4">
    <button id="closeCalendarBtn" class="close-btn" title="Fermer">
      <span>&times;</span>
    </button>
    <div id="calendar"></div>
  </div>
</div>

<div id="calendarOverlay" class="calendar-overlay"></div>
<style>
/* --- Variables de Couleurs Thématiques (Esprit Cyclisme & Strava/Garmin) --- */
:root {
  --bike-yellow: #ffdd00;      /* Jaune Maillot Jaune / Dynamisme */
  --bike-orange: #fc4c02;      /* Orange Strava / Effort / Gravel */
  --bike-green: #00e676;       /* Vert / Vert Sprinteur / Sorties Vertes (VTT) */
  --bike-blue: #00d4ff;        /* Bleu / Sorties Cool / Route / Endurance */
  --bike-dark-bg: #141414;     /* Fond sombre principal */
  --bike-card-bg: #1e1e1e;     /* Fond des éléments */
  --bike-border: rgba(255, 255, 255, 0.08);
}

/* --- Style Général de FullCalendar Customisé --- */
.fc {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  color: #f5f5f5;
  background: var(--bike-dark-bg);
  border-radius: 12px;
  padding: 15px;
}

/* En-têtes des jours (Lun, Mar...) */
.fc .fc-col-header-cell {
  background: rgba(255, 255, 255, 0.03);
  padding: 10px 0;
  text-transform: uppercase;
  font-size: 0.8rem;
  letter-spacing: 1px;
  font-weight: 700;
  color: #aaa;
  border-bottom: 2px solid var(--bike-border);
}

/* Cellules des jours */
.fc .fc-daygrid-day {
  transition: background 0.2s ease;
}
.fc .fc-daygrid-day:hover {
  background: rgba(255, 255, 255, 0.02);
}
.fc .fc-day-today {
  background: rgba(255, 221, 0, 0.05) !important; /* Teinte jaune légère pour aujourd'hui */
}
.fc .fc-day-today .fc-daygrid-day-number {
  background: var(--bike-yellow);
  color: #000;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

/* --- Personnalisation des Événements (Plus vivants, style "Badges") --- */
.fc-daygrid-event {
  border-radius: 6px !important;
  border: none !important;
  padding: 4px 8px !important;
  margin: 2px 5px !important;
  font-weight: 600;
  font-size: 0.85rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.fc-daygrid-event:hover {
  transform: translateY(-1px) scale(1.02);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
}

/* --- Classes de couleurs selon le type d'événement --- 
   (À appliquer dans vos objets d'événements JS via className: 'event-route') */
.event-route {
  background: rgba(0, 212, 255, 0.15) !important;
  color: var(--bike-blue) !important;
  border-left: 4px solid var(--bike-blue) !important;
}
.event-vtt {
  background: rgba(0, 230, 118, 0.15) !important;
  color: var(--bike-green) !important;
  border-left: 4px solid var(--bike-green) !important;
}
.event-gravel {
  background: rgba(252, 76, 2, 0.15) !important;
  color: var(--bike-orange) !important;
  border-left: 4px solid var(--bike-orange) !important;
}
.event-race {
  background: rgba(255, 221, 0, 0.15) !important;
  color: var(--bike-yellow) !important;
  border-left: 4px solid var(--bike-yellow) !important;
}

/* Gestion du point d'événement si affichage en mode point */
.fc-daygrid-dot-event {
  padding: 4px !important;
  display: flex;
  align-items: center;
  flex-direction: row !important; /* Aligné horizontalement pour plus de modernité */
}

/* --- Barre d'outils générale et boutons de navigation --- */
.fc .fc-button-primary {
  background-color: #262626 !important;
  border: 1px solid var(--bike-border) !important;
  color: #fff !important;
  text-transform: capitalize;
  border-radius: 8px !important;
  transition: all 0.2s ease;
}
.fc .fc-button-primary:hover {
  background-color: #333 !important;
  border-color: var(--bike-yellow) !important;
  color: var(--bike-yellow) !important;
}
.fc .fc-button-active {
  background-color: var(--bike-yellow) !important;
  color: #000 !important;
  border-color: var(--bike-yellow) !important;
  font-weight: bold;
}

/* --- Barre d'outils du bouton Agrandir (Votre code optimisé) --- */
.calendar-toolbar {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 12px;
}

.expand-btn {
  background: rgba(255, 221, 0, 0.1);
  border: 1px solid rgba(255, 221, 0, 0.3);
  color: var(--bike-yellow);
  border-radius: 8px;
  width: 42px;
  height: 42px;
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.expand-btn:hover {
  background: var(--bike-yellow);
  color: #000;
  box-shadow: 0 0 15px rgba(255, 221, 0, 0.5);
}

/* --- Overlay assombri derrière le popup --- */
.calendar-overlay {
  position: fixed;
  inset: 0;
  background: rgba(10, 10, 10, 0.85);
  backdrop-filter: blur(8px); /* Flou plus prononcé pour faire ressortir le calendrier */
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.35s ease;
  z-index: 999;
}

.calendar-overlay.active {
  opacity: 1;
  pointer-events: auto;
}

/* --- Conteneur du calendrier --- */
.calendar-container {
  position: relative;
  z-index: 1;
}

/* Mode "Fixé" / Popup ouvert */
.calendar-container.calendar-fixed {
  position: fixed;
  margin: 0;
  z-index: 1000;
  overflow-y: auto;
  border-radius: 16px;
  background: var(--bike-dark-bg);
  border: 1px solid rgba(255, 221, 0, 0.2);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), 0 0 40px rgba(255, 221, 0, 0.15);
  transition: top 0.45s cubic-bezier(0.4, 0, 0.2, 1),
              left 0.45s cubic-bezier(0.4, 0, 0.2, 1),
              width 0.45s cubic-bezier(0.4, 0, 0.2, 1),
              height 0.45s cubic-bezier(0.4, 0, 0.2, 1),
              box-shadow 0.4s ease;
}

/* Empêche le scroll du body quand le popup est ouvert */
body.calendar-locked {
  overflow: hidden;
}

/* --- Croix de fermeture (Style épuré) --- */
.close-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--bike-border);
  color: #fff;
  font-size: 1.3rem;
  display: none;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1001;
  opacity: 0;
  transition: all 0.3s ease;
}

.close-btn:hover {
  background: #fff;
  color: #000;
  transform: scale(1.05);
}

.calendar-container.calendar-fixed .close-btn {
  display: flex;
  opacity: 1;
}

/* --- Redimensionnements internes --- */
.fc-daygrid-body.fc-daygrid-body-unbalanced,
.fc-scrollgrid-sync-table,
.fc-col-header {
  width: 100% !important;
  height: 100% !important;
}

.calendar-container.calendar-fixed #calendar,
.calendar-container.calendar-fixed .fc {
  height: 100% !important;
}

.fc-toolbar-chunk {
  display: flex;
  text-align: center;
  align-items: center;
}

.fc .fc-daygrid-day-number {
  text-decoration: none;
}

/* --- Bouton d'ouverture Mobile --- */
.open-calendar-mobile-btn {
  display: none; /* Cache sur desktop par défaut */
  width: 100%;
  padding: 14px 20px;
  background: var(--bike-dark-bg);
  color: var(--bike-yellow);
  border: 1px solid rgba(255, 221, 0, 0.4);
  border-radius: 12px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  align-items: center;
  justify-content: center;
  gap: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
  transition: all 0.25s ease;
  user-select: none;
}

/* Effet au survol (tablettes/desktop avec écran tactile mixte) */
@media (hover: hover) {
  .open-calendar-mobile-btn:hover {
    background: var(--bike-yellow);
    color: #000;
    box-shadow: 0 6px 20px rgba(255, 221, 0, 0.3);
  }
}

/* Effet lors de l'appui tactile */
.open-calendar-mobile-btn:active {
  transform: scale(0.97);
  background: var(--bike-yellow);
  color: #000;
}

/* --- Comportement Responsive (< 768px) --- */
@media (max-width: 768px) {
  /* Affichage du bouton mobile */
  .open-calendar-mobile-btn {
    display: flex;
  }

  /* Masquer la barre d'outils desktop */
  .calendar-toolbar {
    display: none !important;
  }

  /* Cacher le conteneur du calendrier lorsqu'il n'est pas ouvert */
  .calendar-container:not(.calendar-fixed) {
    display: none !important;
  }

  /* Mode Modale / Plein Écran quand le calendrier est ouvert */
  .calendar-container.calendar-fixed {
    display: flex !important;
    flex-direction: column;
    position: fixed !important;
    inset: 0 !important; /* Remplace top, left, width, height pour assurer 100% plein écran */
    border-radius: 16px !important;
    border: none !important;
    padding: 60px 12px 16px 12px !important;
    box-sizing: border-box !important;
    z-index: 1000 !important;
    background: var(--bike-dark-bg);
    overflow-y: auto;
  }

  /* Adaptations FullCalendar pour petit écran */
  .fc {
    padding: 4px !important;
    height: 100% !important;
    display: flex;
    flex-direction: column;
  }

  /* Barre de navigation du calendrier */
  .fc .fc-toolbar {
    flex-direction: column;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px !important;
  }

  .fc .fc-toolbar-chunk {
    display: flex;
    justify-content: center;
    width: 100%;
  }

  .fc .fc-toolbar-title {
    font-size: 1.15rem !important;
    text-align: center;
  }

  .fc .fc-button {
    padding: 8px 12px !important; /* Boutons plus grands pour un meilleur ciblage tactile */
    font-size: 0.85rem !important;
    border-radius: 8px !important;
  }

  /* En-têtes des jours */
  .fc .fc-col-header-cell {
    font-size: 0.7rem !important;
    padding: 6px 0 !important;
  }

  /* Cellules et évènements */
  .fc .fc-daygrid-day-frame {
    min-height: 40px !important;
  }

  .fc-daygrid-event {
    font-size: 0.72rem !important;
    padding: 2px 5px !important;
    margin: 1px 0 !important;
    border-radius: 4px !important;
  }
}

/* Ajustements supplémentaires pour très petits écrans (< 480px) */
@media (max-width: 480px) {
  .fc .fc-toolbar-title {
    font-size: 1rem !important;
  }

  .fc .fc-button {
    padding: 6px 10px !important;
    font-size: 0.75rem !important;
  }

  .fc-daygrid-event {
    font-size: 0.65rem !important;
  }
}

/* --- Style Général du Bouton Mobile --- */
.open-calendar-mobile-btn {
  display: none; /* Masqué sur PC/Desktop */
  width: 100%;
  padding: 10px 14px;
  background: linear-gradient(135deg, rgba(30, 30, 35, 0.95) 0%, rgba(18, 18, 20, 0.98) 100%);
  border: 1px solid rgba(255, 221, 0, 0.25);
  border-radius: 16px;
  color: #ffffff;
  cursor: pointer;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
  user-select: none;
  -webkit-tap-highlight-color: transparent;
}

/* Effet au clic/appui tactile */
.open-calendar-mobile-btn:active {
  transform: scale(0.97);
  border-color: var(--bike-yellow, #ffdd00);
  box-shadow: 0 4px 15px rgba(255, 221, 0, 0.2);
}

.btn-left-content {
  display: flex;
  align-items: center;
  gap: 14px;
}

/* --- Badge Calendrier Dynamique --- */
.calendar-badge {
  width: 42px;
  height: 44px;
  background: rgba(10, 10, 12, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 11px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  position: relative;
}

/* Haut du calendrier (Accroche orange Strava) */
.calendar-badge-header {
  height: 12px;
  background: linear-gradient(90deg, #fc4c02 0%, #ff6b00 100%);
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  padding: 0 4px;
}

/* Petites reliures métalliques */
.calendar-badge-header .pin {
  width: 3px;
  height: 4px;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 2px;
}

/* Corps avec le chiffre du jour */
.calendar-badge-body {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.02);
}

.calendar-badge-day {
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--bike-yellow, #ffdd00);
  line-height: 1;
  letter-spacing: -0.5px;
  font-family: 'Inter', -apple-system, sans-serif;
}

/* --- Textes --- */
.btn-text-group {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 2px;
}

.btn-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: 0.2px;
}

.btn-subtitle {
  font-size: 0.72rem;
  font-weight: 500;
  color: #8e8e93;
}

/* --- Flèche / Chevron --- */
.btn-arrow {
  width: 32px;
  height: 32px;
  background: rgba(255, 221, 0, 0.08);
  border: 1px solid rgba(255, 221, 0, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--bike-yellow, #ffdd00);
  transition: transform 0.25s ease, background 0.25s ease;
}

.open-calendar-mobile-btn:active .btn-arrow {
  transform: translateX(3px);
  background: var(--bike-yellow, #ffdd00);
  color: #000;
}

/* --- Affichage Responsive (< 768px) --- */
@media (max-width: 768px) {
  .open-calendar-mobile-btn {
    display: flex; /* Rendu visible uniquement sur mobile */
  }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const btn = document.getElementById('expandCalendarBtn');
  const closeBtn = document.getElementById('closeCalendarBtn');
  const wrapper = document.getElementById('calendarWrapper');
  const overlay = document.getElementById('calendarOverlay');
  const icon = btn.querySelector('.expand-icon');

  // Marqueur invisible qui garde la place d'origine du calendrier dans le DOM
  const placeholder = document.createComment('calendar-placeholder');
  wrapper.parentNode.insertBefore(placeholder, wrapper);

  let expanded = false;
  let originalRect = null;

  function resizeCalendar() {
    if (window.calendar && typeof window.calendar.updateSize === 'function') {
      window.calendar.updateSize();
    }
  }

  function getTargetSize() {
    const isMobile = window.innerWidth <= 768;

    const widthRatio = isMobile ? 0.96 : 0.92;
    const heightRatio = isMobile ? 0.94 : 0.9;
    const maxWidth = isMobile ? window.innerWidth : 1300;

    const targetWidth = Math.min(window.innerWidth * widthRatio, maxWidth);
    const targetHeight = window.innerHeight * heightRatio;
    const targetTop = (window.innerHeight - targetHeight) / 2;
    const targetLeft = (window.innerWidth - targetWidth) / 2;

    return { width: targetWidth, height: targetHeight, top: targetTop, left: targetLeft };
  }

  function openCalendar() {
    if (expanded) return;
    expanded = true;

    // 1. Position/taille actuelles, AVANT de sortir le calendrier de son parent
    originalRect = wrapper.getBoundingClientRect();

    // 2. On déplace le calendrier directement dans <body>
    //    pour échapper à tout ancêtre avec transform/filter qui casserait le fixed
    document.body.appendChild(wrapper);

    overlay.classList.add('active');
    document.body.classList.add('calendar-locked');

    // 3. On le replace EXACTEMENT à sa position d'origine (aucun saut visuel)
    wrapper.classList.add('calendar-fixed');
    wrapper.style.transition = 'none';
    wrapper.style.top = originalRect.top + 'px';
    wrapper.style.left = originalRect.left + 'px';
    wrapper.style.width = originalRect.width + 'px';
    wrapper.style.height = originalRect.height + 'px';

    // Force le reflow pour que l'état de départ soit bien pris en compte
    wrapper.offsetHeight;

    // 4. Anime position + taille simultanément vers le format popup centré
    requestAnimationFrame(() => {
      wrapper.style.transition = '';

      const target = getTargetSize();

      wrapper.style.top = target.top + 'px';
      wrapper.style.left = target.left + 'px';
      wrapper.style.width = target.width + 'px';
      wrapper.style.height = target.height + 'px';
    });

    icon.textContent = '✕';
    wrapper.addEventListener('transitionend', resizeCalendar, { once: true });
  }

  function closeCalendar() {
    if (!expanded) return;
    expanded = false;

    overlay.classList.remove('active');
    document.body.classList.remove('calendar-locked');
    icon.textContent = '⤢';

    // Anime position + taille simultanément vers l'état d'origine
    wrapper.style.top = originalRect.top + 'px';
    wrapper.style.left = originalRect.left + 'px';
    wrapper.style.width = originalRect.width + 'px';
    wrapper.style.height = originalRect.height + 'px';

    wrapper.addEventListener('transitionend', function handler() {
      wrapper.classList.remove('calendar-fixed');
      wrapper.style.top = '';
      wrapper.style.left = '';
      wrapper.style.width = '';
      wrapper.style.height = '';
      wrapper.style.transition = '';

      // 5. On remet le calendrier exactement à sa place d'origine dans le DOM
      placeholder.parentNode.insertBefore(wrapper, placeholder.nextSibling);

      resizeCalendar();
    }, { once: true });
  }

  btn.addEventListener('click', () => expanded ? closeCalendar() : openCalendar());
  closeBtn.addEventListener('click', () => { if (expanded) closeCalendar(); });
  overlay.addEventListener('click', () => { if (expanded) closeCalendar(); });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && expanded) closeCalendar();
  });

  window.addEventListener('resize', () => {
    if (!expanded) return;
    const target = getTargetSize();
    wrapper.style.top = target.top + 'px';
    wrapper.style.left = target.left + 'px';
    wrapper.style.width = target.width + 'px';
    wrapper.style.height = target.height + 'px';
    resizeCalendar();
  });
});

document.addEventListener('DOMContentLoaded', function () {
  const wrapper = document.getElementById('calendarWrapper');
  const overlay = document.getElementById('calendarOverlay');
  const openMobileBtn = document.getElementById('openMobileBtn');
  const expandBtn = document.getElementById('expandCalendarBtn');
  const closeBtn = document.getElementById('closeCalendarBtn');

  // Initialisation de FullCalendar
  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek'
    },
    // Vos événements ici...
  });
  calendar.render();

  // Fonction pour ouvrir le modal
  function openCalendarModal() {
    wrapper.classList.add('calendar-fixed');
    overlay.classList.add('active');
    document.body.classList.add('calendar-locked');
    setTimeout(() => calendar.updateSize(), 100); // Recalcule la taille du calendrier
  }

  // Fonction pour fermer le modal
  function closeCalendarModal() {
    wrapper.classList.remove('calendar-fixed');
    overlay.classList.remove('active');
    document.body.classList.remove('calendar-locked');
    setTimeout(() => calendar.updateSize(), 100);
  }

  // Événements de clics
  if (openMobileBtn) openMobileBtn.addEventListener('click', openCalendarModal);
  if (expandBtn) expandBtn.addEventListener('click', openCalendarModal);
  if (closeBtn) closeBtn.addEventListener('click', closeCalendarModal);
  if (overlay) overlay.addEventListener('click', closeCalendarModal);
});

document.addEventListener('DOMContentLoaded', () => {
  // 1. Récupération du jour actuel
  const now = new Date();
  const dayNumber = now.getDate();
  
  // Formatage sur 2 chiffres (ex: 01, 09, 15)
  const formattedDay = dayNumber < 10 ? `0${dayNumber}` : dayNumber;

  // 2. Injection dans le bouton
  const dayElement = document.getElementById('mobileBtnDay');
  if (dayElement) {
    dayElement.textContent = formattedDay;
  }
});
</script>
                  <?php endif; ?>
                  <?php foreach ($contenus as $contenu): ?>
                    <?php if ($contenu->getPage()->getId() != 6): ?>
                      <div id="progress-bar"></div>
                      <header class="header-premium">
                        <div class="header-content">
                          <h1 class="main-title">
                            <?= $contenu->getNom() ?>
                          </h1>
                          <div class="divider"></div>
                        </div>
                      </header>
                      <?php if ($contenu->getTexte()): ?>
                        <div>
                          <?= $contenu->getTexte() ?>
                        </div>
                      <?php endif; ?>
                      <?php if ($contenu->getImage()): ?>
                        <div>
                          <img src="/<?= htmlspecialchars($contenu->getImage()) ?>" alt="Aperçu"
                            style="width: 100%; height: 100%; max-width: none !important; object-fit: cover; border-radius: 24px;"
                            class="img-thumbnail">
                        </div>
                      <?php endif; ?>
                      <?php if ($contenu->getVideo()): ?>
                        <div style="width: 100%; height: 100%;">
                          <video src="/<?= htmlspecialchars($contenu->getVideo()) ?>" controls class="img-thumbnail"
                            style="width: 100%; height: 100%; max-width: none !important; object-fit: cover; border-radius: 24px;">
                            Votre navigateur ne prend pas en charge la lecture de vidéos.
                          </video>
                        </div>
                      <?php endif; ?>
                      <?php if ($contenu->getPdf()): ?>
                        <div id="viewer-container-<?= $contenu->getId() ?>" class="reveal delay-2"></div>

                        <script type="module">
                          import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.mjs';

                          // Configuration du worker indispensable
                          pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.worker.mjs';

                          const url<?= $contenu->getId() ?> = '/<?= $contenu->getPdf() ?>'; // METTEZ VOTRE LIEN ICI
                          const container<?= $contenu->getId() ?> = document.getElementById('viewer-container-<?= $contenu->getId() ?>');
                          const progressBar = document.getElementById('progress-bar');

                          async function initPDF<?= $contenu->getId() ?>() {
                            try {
                              const loadingTask = pdfjsLib.getDocument(url<?= $contenu->getId() ?>);
                              const pdf = await loadingTask.promise;

                              for (let i = 1; i <= pdf.numPages; i++) {
                                await renderPage<?= $contenu->getId() ?>(pdf, i);
                                // Mise à jour de la barre de progression
                                progressBar.style.width = `${(i / pdf.numPages) * 97}%`;
                              }
                            } catch (error) {
                              console.error("Erreur critique:", error);
                              document.getElementById('loader').innerText = "Fichier introuvable ou erreur de sécurité.";
                            }
                          }

                          async function renderPage<?= $contenu->getId() ?>(pdf, num) {
                            const page = await pdf.getPage(num);
                            const viewport = page.getViewport({ scale: 2 }); // Qualité Retina

                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;

                            container<?= $contenu->getId() ?>.appendChild(canvas);

                            await page.render({ canvasContext: context, viewport: viewport }).promise;

                            // Déclenche l'animation
                            requestAnimationFrame(() => canvas.classList.add('loaded'));
                          }

                          initPDF<?= $contenu->getId() ?>();
                        </script>
                      <?php endif; ?>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>

                <div class="sticky-sidebar">
                  <?php foreach ($contenus as $contenu): ?>
                    <?php if ($page->getId() == 6): ?>
                      <?php $i++ ?>
                      <div class="card text-dark shadow-sm notice-box action-card reveal delay-<?= $i ?>"
                        style="animation-delay: 0.<?= $i ?>s;">
                        <?php if ($contenu->getTexte()): ?>
                          <div>
                            <?= $contenu->getTexte() ?>
                          </div>
                        <?php endif; ?>
                        <?php if ($contenu->getImage()): ?>
                          <div>
                            <img src="/<?= htmlspecialchars($contenu->getImage()) ?>" alt="Aperçu"
                              style="width: 100%; height: 100%; max-width: none !important; object-fit: cover; border-radius: 24px;"
                              class="img-thumbnail">
                          </div>
                        <?php endif; ?>
                        <?php if ($contenu->getVideo()): ?>
                          <div style="width: 100%; height: 100%;">
                            <video src="/<?= htmlspecialchars($contenu->getVideo()) ?>" controls class="img-thumbnail"
                              style="width: 100%; height: 100%; max-width: none !important; object-fit: cover; border-radius: 24px;">
                              Votre navigateur ne prend pas en charge la lecture de vidéos.
                            </video>
                          </div>
                        <?php endif; ?>
                        <?php if ($contenu->getPdf()): ?>
                          <div id="viewer-container-<?= $contenu->getId() ?>" class="reveal delay-2"></div>

                          <script type="module">
                            import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.mjs';

                            // Configuration du worker indispensable
                            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.worker.mjs';

                            const url<?= $contenu->getId() ?> = '/<?= $contenu->getPdf() ?>'; // METTEZ VOTRE LIEN ICI
                            const container<?= $contenu->getId() ?> = document.getElementById('viewer-container-<?= $contenu->getId() ?>');
                            const progressBar = document.getElementById('progress-bar');

                            async function initPDF<?= $contenu->getId() ?>() {
                              try {
                                const loadingTask = pdfjsLib.getDocument(url<?= $contenu->getId() ?>);
                                const pdf = await loadingTask.promise;

                                for (let i = 1; i <= pdf.numPages; i++) {
                                  await renderPage<?= $contenu->getId() ?>(pdf, i);
                                  // Mise à jour de la barre de progression
                                  progressBar.style.width = `${(i / pdf.numPages) * 97}%`;
                                }
                              } catch (error) {
                                console.error("Erreur critique:", error);
                                document.getElementById('loader').innerText = "Fichier introuvable ou erreur de sécurité.";
                              }
                            }

                            async function renderPage<?= $contenu->getId() ?>(pdf, num) {
                              const page = await pdf.getPage(num);
                              const viewport = page.getViewport({ scale: 2 }); // Qualité Retina

                              const canvas = document.createElement('canvas');
                              const context = canvas.getContext('2d');
                              canvas.height = viewport.height;
                              canvas.width = viewport.width;

                              container<?= $contenu->getId() ?>.appendChild(canvas);

                              await page.render({ canvasContext: context, viewport: viewport }).promise;

                              // Déclenche l'animation
                              requestAnimationFrame(() => canvas.classList.add('loaded'));
                            }

                            initPDF<?= $contenu->getId() ?>();
                          </script>

                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>
                  <?php if (!empty($eventsForPage)): ?>
                    <!-- <div class="row" id="comptes-rendus"> -->
                    <div id="comptes-rendus">
                      <?php $i = 0; ?>
                      <?php foreach ($eventsPage as $event): ?>
                        <?php if ($event->getCategorieEvent()->getUrl() == $page->getUrl() && $event->getCompteRendu() != null): ?>

                          <?php
                          // Chemin du fichier GPX (ajuster la logique d'accès à la BDD si besoin)
                          $gpxFilePath = $event->getGpxFilePath();
                          // Optionnel : Si BDD ne contient que le nom du fichier
                          // if (!empty($gpxFilePath) && !str_starts_with($gpxFilePath, '/uploads/gpx/')) {
                          //     $gpxFilePath = '/uploads/gpx/' . $gpxFilePath;
                          // }
                          $mapId = 'gpx-map-' . $event->getId();
                          ?>

                          <!-- <div class="col-md-12 mb-4"> -->
                          <div>
                            <?php $i++ ?>
                            <div class="card text-dark shadow-sm notice-box action-card reveal delay-<?= $i ?>"
                              style="animation-delay: 0.<?= $i ?>s;">
                              <div class="card-body">

                                <?php
                                // Préparation des variables GPX pour injection
                                $gpxFilePath = $event->getGpxFilePath();
                                if (!empty($gpxFilePath) && !str_starts_with($gpxFilePath, '/uploads/gpx/')) {
                                  $gpxFilePath = '/uploads/gpx/' . $gpxFilePath;
                                }
                                $mapId = 'gpx-map-' . $event->getId();
                                $gpxExists = !empty($gpxFilePath);

                                // Contenu (texte) prend 10 colonnes, Carte (vignette) prend 2 colonnes
                                $contentColClass = $gpxExists ? 'col-md-10' : 'col-md-12';
                                ?>

                                <div class="row">
                                  <div class="<?= $contentColClass ?> d-flex flex-column justify-content-between">
                                    <a href="/page/<?= $page->getUrl() ?>/compte-rendu/<?= $event->getId() ?>"
                                      class="text-decoration-none text-dark h-100">
                                      <div>
                                        <p class="card-text">
                                          <small class="fw-bold" style="color: #0087ff">
                                            <?= $event->getDateStart() ? $event->getDateStart()->format('d/m/Y') : '' ?>
                                            <?= $event->getDateEnd() ? ' - ' . $event->getDateEnd()->format('d/m/Y') : '' ?>
                                          </small>
                                        </p>
                                        <?= $event->getTitre() ?>
                                        <p class="card-text">
                                          <?= htmlspecialchars(mb_substr(trim(strip_tags($event->getCompteRendu())), 0, mb_strrpos(mb_substr(trim(strip_tags($event->getCompteRendu())), 0, 100), ' ') ?: 100)) . (mb_strlen(trim(strip_tags($event->getCompteRendu()))) > 100 ? '…' : '') ?>
                                        </p>
                                      </div>
                                    </a>
                                  </div>

                                  <?php if ($gpxExists): ?>
                                    <a href="/page/<?= $page->getUrl() ?>/compte-rendu/<?= $event->getId() ?>#gpx"
                                      class="col-md-2 d-flex align-items-center justify-content-center">
                                      <div id="<?= $mapId ?>"
                                        style="width: 50%; height: 100px; border-radius: 8px; border: 1px solid #ddd;"></div>
                                    </a>
                                  <?php endif; ?>
                                </div>

                              </div>
                            </div>
                            <?php if ($gpxExists): ?>
                              <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                  const mapElement = document.getElementById('<?= $mapId ?>');

                                  if (mapElement) {
                                    // 1. Initialisation de la carte avec les options minimalistes
                                    const map = L.map('<?= $mapId ?>', {
                                      attributionControl: false, // Cache l'attribution (minimalisme)
                                      zoomControl: false,        // Cache les boutons de zoom
                                      dragging: false,           // Désactive le déplacement
                                      scrollWheelZoom: false,    // Désactive le zoom à la molette
                                      doubleClickZoom: false,    // Désactive le double-clic pour zoomer
                                      tap: false                 // Désactive le tap sur mobile (minimalisme)
                                    });

                                    // IMPORTANT : Force le recalcul de la taille après le chargement
                                    setTimeout(() => { map.invalidateSize(true); }, 100);

                                    // 2. Tuiles minimalistes (Utilisation des tuiles de Stadia Maps Toner Lite pour un fond gris/monochrome)
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                      maxZoom: 18,
                                      minZoom: 2,
                                      attribution: ''
                                    }).addTo(map);

                                    // 3. Chargement de la trace GPX
                                    new L.GPX("<?= htmlspecialchars($gpxFilePath, ENT_QUOTES, 'UTF-8') ?>", {
                                      async: true,
                                      // Ligne fine et couleur sobre
                                      polyline_options: { color: '#0087ff', opacity: 0.8, weight: 2 },
                                      // Suppression de tous les marqueurs pour un affichage minimaliste
                                      marker_options: {
                                        startIconUrl: null,
                                        endIconUrl: null,
                                        shadowUrl: null
                                      }
                                    }).on('loaded', function (e) {
                                      // 4. Ajustement de la vue pour qu'elle contienne toute la trace (avec un petit padding)
                                      map.fitBounds(e.target.getBounds(), { padding: [10, 10] });
                                    }).addTo(map);
                                  }
                                });
                              </script>
                            <?php endif; ?>
                          </div>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
          <?php
          // Début du Bloc PHP/HTML 
          if ($page->getId() == 5 && !empty($medias)):
            $imagesForLightbox = array_filter($medias, fn($m) => $m->getType() === 'image');
            $totalImages = count($imagesForLightbox);
            $imageIndex = 0;
            ?>
            <style>
              /* ================================================================= */
              /* 1. STYLES GÉNÉRAUX & FILTRAGE */
              /* ================================================================= */
              .media-item-col {
                transition: all 0.4s ease-in-out;
                display: block;
              }

              .media-item-col.hidden {
                opacity: 0;
                transform: scale(0.85);
                height: 0;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                overflow: hidden;
              }

              /* ================================================================= */
              /* 2. STYLES DES CARTES & ANIMATIONS */
              /* ================================================================= */
              .media-card,
              .video-card {
                border: none;
                transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                cursor: pointer;
              }

              .media-card:hover,
              .video-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.2) !important;
              }

              .media-card .card-img-top-wrap {
                height: 250px;
                display: block;
                overflow: hidden;
                position: relative;
              }

              @media (max-width: 576px) {
                .media-card .card-img-top-wrap {
                  height: 180px;
                }
              }

              .media-card .media-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.6s ease-out;
              }

              .media-card:hover .media-image {
                transform: scale(1.15);
              }

              .media-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 123, 255, 0.6);
                display: flex;
                justify-content: center;
                align-items: center;
                opacity: 0;
                transition: opacity 0.3s ease;
                z-index: 5;
              }

              .media-card:hover .media-overlay {
                opacity: 1;
              }

              .media-overlay-video {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                opacity: 1;
                transition: opacity 0.3s ease;
                z-index: 5;
              }

              .video-card:hover .media-overlay-video {
                opacity: 0;
              }

              .media-badge {
                opacity: 0.95;
                font-size: 0.7rem;
                padding: 0.4em 0.8em;
                border-radius: 50rem !important;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
              }

              /* ================================================================= */
              /* 3. LIGHTBOX MODALE (MODERNE & TRANSPARENT) */
              /* ================================================================= */

              .modal {
                display: none;
                position: fixed;
                z-index: 9999;
                padding-top: 0;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow-y: hidden;
                /* EFFET MODERNE : Fond semi-transparent avec flou */
                background-color: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
              }

              .modal-content {
                position: relative;
                margin: 0 auto;
                padding: 0;
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
              }

              /* Conteneur de la zone de visualisation (fenêtre du carousel) */
              .slideshow-container {
                width: 100%;
                max-height: 85vh;
                position: relative;
                margin: auto;
                overflow: hidden;
                display: flex;
                align-items: center;
              }

              /* Conteneur INTERNE qui va GLISSER */
              .slideshow-inner-container {
                display: flex;
                width: 100%;
                height: 100%;
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
              }

              /* Les diapositives individuelles */
              .mySlides {
                flex: 0 0 100%;
                width: 100%;
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
              }

              /* Bouton Fermer (Plus discret) */
              .close {
                color: white !important;
                position: fixed;
                top: 15px;
                right: 15px;
                font-size: 40px;
                font-weight: 300;
                opacity: 0.8;
                transition: opacity 0.2s;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                z-index: 10000;
                background: rgba(0, 0, 0, 0.3);
                border-radius: 50%;
                width: 50px;
                height: 50px;
                line-height: 50px;
                text-align: center;
              }

              .close:hover,
              .close:focus {
                opacity: 1;
                background: rgba(0, 0, 0, 0.5);
                text-decoration: none;
                cursor: pointer;
              }

              /* Boutons Précédent et Suivant (Intégrés) */
              .prev,
              .next {
                cursor: pointer;
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 60px;
                height: 100%;
                padding: 0;
                color: white;
                font-weight: bold;
                font-size: 40px;
                user-select: none;
                transition: background-color 0.3s ease;
                background-color: transparent;
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10;
                opacity: 0.8;
              }

              .prev:hover,
              .next:hover {
                background-color: rgba(0, 0, 0, 0.15);
                opacity: 1;
              }

              .prev {
                left: 0;
              }

              .next {
                right: 0;
              }

              .prev i,
              .next i {
                text-shadow: 0 0 10px rgba(0, 0, 0, 0.8);
              }


              /* Assurer la responsivité de l'image principale dans le modal (Inchangée) */
              .modal-content img {
                height: auto;
                max-height: 85vh;
                object-fit: contain;
                margin: 0 auto;
                display: block;
              }

              /* Miniatures */
              .modal-thumbnails {
                max-height: 100px;
                overflow-y: hidden;
                overflow-x: auto;
                white-space: nowrap;
                display: flex;
                justify-content: center;
                background: rgba(0, 0, 0, 0.7);
                padding: 10px 0;
                /* Centrer verticalement */
                width: 100%;
                position: relative;
                left: 12px;
              }

              .modal-thumbnails img {
                height: 70px;
                width: auto !important;
                object-fit: cover;
                opacity: 0.7;
                transition: opacity 0.3s ease;
                border: 2px solid transparent;
              }

              .modal-thumbnails .demo:hover,
              .modal-thumbnails .demo.active {
                opacity: 1;
                border-color: #007bff;
              }

              /* Légende et Compteur (AMÉLIORÉ) */
              .caption-container {
                padding: 15px 20px;
                text-align: center;
                background-color: rgba(0, 0, 0, 0.8);
                color: white;
                font-size: 1.1rem;
                position: absolute;
                bottom: 0;
                width: 100%;
                z-index: 15;
              }

              .caption-container p {
                margin: 0;
                font-weight: 300;
                display: flex;
                justify-content: center;
                align-items: baseline;
              }

              .caption-counter {
                font-weight: bold;
                margin-left: 10px;
                opacity: 0.8;
                font-size: 0.9em;
                /* Légèrement plus petit que le titre */
              }

              /* Media Query Mobile */
              @media (max-width: 768px) {

                .prev,
                .next {
                  font-size: 30px;
                  width: 40px;
                  top: 45%;
                  background-color: rgba(0, 0, 0, 0.2);
                }

                .close {
                  font-size: 30px;
                  width: 40px;
                  height: 40px;
                  line-height: 40px;
                  top: 10px;
                  right: 10px;
                  background: rgba(0, 0, 0, 0.5);
                }

                .caption-container {
                  padding: 10px;
                  font-size: 0.9rem;
                }

                .modal-content img {
                  max-height: 75vh;
                }

                .modal-thumbnails img {
                  height: 50px;
                }
              }
            </style>

            <div class="container-md-fluid content-section-page py-5">
              <div class="container-md content">

                <div class="d-flex flex-md-row flex-column justify-content-md-center mb-5 gap-3 media-filter-bar">
                  <button class="btn btn-primary btn-filter active rounded-pill px-4" data-filter="all">
                    <i class="fas fa-list me-1"></i> Tout Afficher
                  </button>
                  <button class="btn btn-outline-info btn-filter rounded-pill px-4" data-filter="image">
                    <i class="fas fa-images me-1"></i> Photos
                  </button>
                  <button class="btn btn-outline-info btn-filter rounded-pill px-4" data-filter="video">
                    <i class="fas fa-video me-1"></i> Vidéos
                  </button>
                </div>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5" id="media-gallery-modern">
                  <?php foreach ($medias as $media): ?>
                    <?php if ($media->getType() === 'image'): $imageIndex++; ?>
                      <div class="col media-item-col image-item" data-type="image" data-aos="fade-up">
                        <figure class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden media-card">
                          <a href="#" onclick="openModal(); currentSlide(<?= $imageIndex ?>); return false;"
                            class="d-block card-img-top-wrap">
                            <img src="/<?= htmlspecialchars($media->getFichier()) ?>"
                              alt="<?= htmlspecialchars($media->getTitre()) ?>" class="card-img-top media-image"
                              loading="lazy">
                            <div class="media-overlay"><i class="fas fa-search-plus fa-3x text-white"></i></div>
                            <span class="badge bg-primary media-badge position-absolute top-0 end-0 m-2"><i
                                class="fas fa-camera"></i>
                              Photo</span>
                          </a>
                          <figcaption class="card-body p-3 bg-light text-center">
                            <p class="card-text small text-truncate mb-0 text-secondary fw-bold">
                              <?= htmlspecialchars($media->getTitre()) ?>
                            </p>
                          </figcaption>
                        </figure>
                      </div>
                    <?php elseif ($media->getType() === 'video'):
                      $embedUrl = $media->getEmbedUrl();
                      $isExternalVideo = !empty($embedUrl);
                      $videoGridClass = 'col-12 col-md-6 col-lg-6';
                      ?>
                      <div class="<?= $videoGridClass ?> media-item-col video-item" data-type="video" data-aos="fade-up"
                        data-aos-delay="100">
                        <figure class="card h-100 shadow-lg border-0 rounded-4 overflow-hidden video-card position-relative">

                          <div class="ratio ratio-16x9">
                            <?php if ($isExternalVideo): ?>
                              <iframe src="<?= htmlspecialchars($embedUrl) ?>"
                                title="<?= htmlspecialchars($media->getTitre()) ?>"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                              </iframe>
                            <?php else: ?>
                              <video src="/<?= htmlspecialchars($media->getFichier()) ?>" controls class="video-element"
                                poster="chemin/vers/votre/thumbnail.jpg">
                                Votre navigateur ne prend pas en charge la lecture de vidéos.
                              </video>
                            <?php endif; ?>

                            <div class="media-overlay-video">
                              <div class="play-button-wrapper">
                                <i class="fas fa-play"></i>
                              </div>
                            </div>

                            <span class="badge rgba-black-strong media-badge position-absolute top-0 end-0 m-3 shadow-sm">
                              <i class="fas fa-film me-1"></i> Vidéo
                            </span>
                          </div>

                          <figcaption class="card-body p-3 bg-dark text-white text-center">
                            <p class="card-text small mb-0 text-truncate fw-bold">
                              <?= htmlspecialchars($media->getTitre()) ?>
                            </p>
                          </figcaption>
                        </figure>
                      </div>
                      <style>
                        /* Container de la carte */
                        .video-card {
                          transition: transform 0.3s ease;
                          cursor: pointer;
                        }

                        .video-card:hover {
                          transform: translateY(-5px);
                        }

                        /* Vidéo style */
                        .video-element {
                          width: 100%;
                          height: 100%;
                          object-fit: cover;
                        }

                        /* Overlay sombre */
                        .media-overlay-video {
                          position: absolute;
                          inset: 0;
                          /* Top, Left, Right, Bottom à 0 */
                          background: rgba(0, 0, 0, 0.4);
                          display: flex;
                          justify-content: center;
                          align-items: center;
                          opacity: 1;
                          transition: all 0.4s ease;
                          z-index: 2;
                          pointer-events: none;
                          /* Permet de cliquer sur la vidéo à travers l'overlay */
                        }

                        /* Bouton de lecture stylisé */
                        .play-button-wrapper {
                          width: 70px;
                          height: 70px;
                          background: rgba(255, 255, 255, 0.2);
                          backdrop-filter: blur(5px);
                          border-radius: 50%;
                          display: flex;
                          justify-content: center;
                          align-items: center;
                          border: 2px solid white;
                          transition: all 0.3s ease;
                        }

                        .play-button-wrapper i {
                          color: white;
                          font-size: 1.5rem;
                          margin-left: 5px;
                          /* Centrage optique du triangle */
                        }

                        /* Cache l'overlay au survol OU quand la vidéo tourne */
                        .video-card:hover .media-overlay-video,
                        .video-card.is-playing .media-overlay-video {
                          opacity: 0;
                          background: rgba(0, 0, 0, 0);
                          pointer-events: none;
                          /* Optionnel : permet de cliquer "à travers" l'overlay */
                        }

                        /* Grossit le bouton au survol OU quand la vidéo tourne */
                        .video-card:hover .play-button-wrapper,
                        .video-card.is-playing .play-button-wrapper {
                          transform: scale(1.2);
                        }
                      </style>
                      <script>
                        const videoCard = document.querySelector('.video-card');
                        const video = videoCard.querySelector('video');

                        // Quand la vidéo commence à jouer
                        video.addEventListener('play', () => {
                          videoCard.classList.add('is-playing');
                        });

                        // Quand la vidéo est en pause ou terminée
                        video.addEventListener('pause', () => {
                          videoCard.classList.remove('is-playing');
                        });
                      </script>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <div id="myModal" class="modal">

              <span class="close cursor" onclick="closeModal()">&times;</span>

              <div class="modal-content">

                <div class="slideshow-container">

                  <div class="slideshow-inner-container" id="slideshowInnerContainer">

                    <?php
                    $imageIndex = 0;
                    foreach ($imagesForLightbox as $media):
                      $imageIndex++;
                      ?>
                      <div class="mySlides">
                        <img src="/<?= htmlspecialchars($media->getFichier()) ?>" class="img-fluid"
                          alt="<?= htmlspecialchars($media->getTitre()) ?>">
                      </div>
                    <?php endforeach; ?>

                  </div>
                  <a class="prev" onclick="plusSlides(-1)"><i class="fas fa-chevron-left"></i></a>
                  <a class="next" onclick="plusSlides(1)"><i class="fas fa-chevron-right"></i></a>

                  <div class="caption-container">
                    <p id="caption"></p>
                  </div>
                </div>

                <div class="row modal-thumbnails mt-3">
                  <?php
                  $imageIndex = 0;
                  foreach ($imagesForLightbox as $media):
                    $imageIndex++;
                    ?>
                    <div class="col-3 col-md-2 p-1">
                      <img class="demo cursor hover-shadow" src="/<?= htmlspecialchars($media->getFichier()) ?>"
                        style="width:100%; cursor: pointer;" onclick="currentSlide(<?= $imageIndex ?>)"
                        alt="<?= htmlspecialchars($media->getTitre()) ?>">
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
            <script>
              document.addEventListener('DOMContentLoaded', function () {

                // ---------------------------------------------------
                // 1. Initialisation et gestion de la Lightbox (Glissement)
                // ---------------------------------------------------

                let slideIndex = 1;
                const modal = document.getElementById("myModal");
                const slides = document.querySelectorAll(".mySlides");
                const dots = document.querySelectorAll(".demo");
                const captionText = document.getElementById("caption");
                const innerContainer = document.getElementById("slideshowInnerContainer");

                // Fonction pour ouvrir le modal
                window.openModal = function () {
                  if (modal) {
                    modal.style.display = "block";
                    showSlides(1); // Afficher la première slide au démarrage
                  }
                }

                // Fonction pour fermer le modal
                window.closeModal = function () {
                  if (modal) {
                    modal.style.display = "none";
                  }
                }

                // Navigation (Précédent/Suivant)
                window.plusSlides = function (n) {
                  showSlides(slideIndex + n);
                }

                // Navigation (Miniatures)
                window.currentSlide = function (n) {
                  showSlides(n);
                }

                // Affichage principal de la diapositive (UTILISE TRANSFORM)
                function showSlides(n) {
                  // Bouclage
                  if (n > slides.length) {
                    slideIndex = 1;
                  } else if (n < 1) {
                    slideIndex = slides.length;
                  } else {
                    slideIndex = n;
                  }

                  // CALCUL DU GLISSEMENT : Déplace le conteneur intérieur
                  const translateValue = -(slideIndex - 1) * 100;
                  if (innerContainer) {
                    innerContainer.style.transform = `translateX(${translateValue}%)`;
                  }

                  // Désactiver toutes les miniatures
                  dots.forEach(dot => {
                    dot.classList.remove("active");
                  });

                  // Activer la miniature et GÉNÉRER la légende/le compteur
                  if (dots.length > 0) {
                    const currentDot = dots[slideIndex - 1];
                    currentDot.classList.add("active");

                    // Logique de fusion du titre et du compteur
                    const totalSlides = slides.length;
                    const imageTitle = currentDot.alt ? currentDot.alt : 'Sans titre';

                    const contentHtml = `${imageTitle} <span class="caption-counter">${slideIndex} / ${totalSlides}</span>`;

                    captionText.innerHTML = contentHtml;
                  }
                }

                // Fermer le modal en appuyant sur la touche ESC
                document.addEventListener('keydown', function (event) {
                  if (event.key === "Escape" && modal && modal.style.display === "block") {
                    closeModal();
                  }
                });

                // ---------------------------------------------------
                // 2. Gestion du Filtrage (Requiert jQuery)
                // ---------------------------------------------------
                if (typeof jQuery !== 'undefined') {
                  $('.btn-filter').on('click', function () {
                    $('.btn-filter').removeClass('active btn-primary').addClass('btn-outline-info');
                    $(this).removeClass('btn-outline-info').addClass('active btn-primary');

                    const filterType = $(this).data('filter');

                    $('#media-gallery-modern .media-item-col').each(function () {
                      const itemType = $(this).data('type');

                      if (filterType === 'all' || itemType === filterType) {
                        $(this).removeClass('hidden').show();
                      } else {
                        $(this).addClass('hidden').hide();
                      }
                    });
                  });
                }
              });
            </script>

          <?php endif; ?>