<template>
  <div class="home-page">
    <!-- НАВИГАЦИЯ -->
    <nav class="topnav">
      <span class="nav-brand" @click="scrollToSection('hero')">Sāpņu Māja</span>
      <ul class="nav-links">
        <li><button :class="{ active: activeSection === 'hero' }" @click="scrollToSection('hero')">Sākums</button></li>
        <li><button :class="{ active: activeSection === 'kategorijas' }" @click="scrollToSection('kategorijas')">Kategorijas</button></li>
        <li><button :class="{ active: activeSection === 'about' }" @click="scrollToSection('about')">Par mums</button></li>
      </ul>
    </nav>

    <!-- ГЛАВНЫЙ ЭКРАН (HERO) -->
    <section id="sec-hero" class="s-hero">
      <div class="hero-bg" :style="{ backgroundImage: 'url(/nenav-rr.jpg)' }"></div>
      <div class="hero-body" :class="{ 'hero-visible': heroVisible }">
        <h1 class="hero-title">Atrodi savu<br>sapņu māju šeit</h1>
        <p class="hero-sub">Rūpīgi atlasīta privātmāju, dzīvokļu un lauku īpašumu kolekcija visā Latvijā.</p>
        <button class="btn-cta" @click="goToCatalog">Apskatīt katalogu <span class="arr">→</span></button>
      </div>
    </section>

    <!-- КАТЕГОРИИ -->
    <section id="sec-kategorijas" class="s-kategorijas">
      <div class="s-inner">
        <p class="s-eyebrow">Kategorijas</p>
        <h2 class="s-title">Ko mēs piedāvājam</h2>
        <p class="s-desc">Trīs īpašumu kategorijas — katra rūpīgi atlasīta, lai atspoguļotu raksturu un kvalitāti.</p>
        <div class="kat-cards">
          <div class="kat-card">
            <span class="kat-icon">
              <svg viewBox="0 0 24 24">
                <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/>
                <path d="M9 21V12h6v9"/>
              </svg>
            </span>
            <h3>Privātmājas</h3>
            <p>Ekskluzīvas savrupmājas un villas ar privātumu un raksturu.</p>
          </div>
          <div class="kat-card">
            <span class="kat-icon">
              <svg viewBox="0 0 24 24">
                <rect x="3" y="3" width="18" height="18" rx="1"/>
                <line x1="9" y1="3" x2="9" y2="21"/>
                <line x1="15" y1="3" x2="15" y2="21"/>
                <line x1="3" y1="9" x2="21" y2="9"/>
                <line x1="3" y1="15" x2="21" y2="15"/>
              </svg>
            </span>
            <h3>Dzīvokļi</h3>
            <p>Dzīvokļi prestižos jaunajos projektos Rīgas centrā.</p>
          </div>
          <div class="kat-card">
            <span class="kat-icon">
              <svg viewBox="0 0 24 24">
                <path d="M12 3C8 8 5 11 5 14a7 7 0 0014 0c0-3-3-6-7-11z"/>
                <line x1="12" y1="21" x2="12" y2="14"/>
              </svg>
            </span>
            <h3>Dabā</h3>
            <p>Lauku īpašumi, mājas pie ezera un meža klusumā.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- О НАС -->
    <section id="sec-about" class="s-about">
      <div class="about-card">
        <p class="about-eyebrow">Par mums</p>
        <h2 class="about-title">Laipni lūdzam tiešsaiste</h2>
        <p class="about-sub">Sāpņu Māja</p>
        <p class="about-desc">Cienījamie lietotāji, jūs varat apskatīt māju katalogu un isu informāciju, varat rakstīt un apskatīt atsauksmes.</p>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: "Home",
  data() {
    return {
      activeSection: "hero",
      heroVisible: false
    };
  },
  mounted() {
    // Анимация появления текста
    setTimeout(() => {
      this.heroVisible = true;
    }, 100);

    // Отслеживание активной секции при скролле
    this.initScrollSpy();
  },
  beforeDestroy() {
    if (this.observer) {
      this.observer.disconnect();
    }
  },
  methods: {
    scrollToSection(sectionId) {
      const element = document.getElementById(`sec-${sectionId}`);
      if (element) {
        element.scrollIntoView({ behavior: "smooth" });
      }
    },
    goToCatalog() {
      // Здесь будет переход на страницу каталога
      this.$router.push("/catalog");
    },
    initScrollSpy() {
      this.observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              const id = entry.target.id.replace("sec-", "");
              this.activeSection = id;
            }
          });
        },
        { threshold: 0.4 }
      );

      const sections = ["hero", "kategorijas", "about"];
      sections.forEach((section) => {
        const el = document.getElementById(`sec-${section}`);
        if (el) {
          this.observer.observe(el);
        }
      });
    }
  }
};
</script>

<style scoped>
:root {
  
  --fb: 'Encode Sans Semi Expanded', sans-serif;
  --cream: #f0ebe2;
  --dark: #1a1a18;
  --forest: #2d4a35;
  --ease: cubic-bezier(0.4, 0, 0.2, 1);
}

/* НАВИГАЦИЯ */
.topnav {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 62px;
  background: rgba(240, 235, 226, 0.96);
  backdrop-filter: blur(14px);
  border-bottom: 1px solid rgba(26, 26, 24, 0.09);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 64px;
  z-index: 1000;
}

.nav-brand {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: 1.15rem;
  font-weight: 500;
  font-style: italic;
  color: #1a1a18;
  letter-spacing: 0.02em;
  cursor: pointer;
}

.nav-links {
  display: flex;
  gap: 48px;
  list-style: none;
}

.nav-links li button {
  font-family: 'Encode Sans Semi Expanded';
  font-size: 0.64rem;
  font-weight: 600;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: #1a1a18;
  background: none;
  border: none;
  cursor: pointer;
  padding-bottom: 5px;
  position: relative;
  opacity: 0.45;
  transition: opacity 0.25s ease;
}

.nav-links li button::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 1px;
  background: #1a1a18;
  transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.nav-links li button:hover {
  opacity: 0.75;
}

.nav-links li button.active {
  opacity: 1;
}

.nav-links li button.active::after {
  width: 100%;
}

/* ГЛАВНЫЙ ЭКРАН */
.s-hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center 35%;
  filter: blur(4px) brightness(0.65);
  transform: scale(1.06);
}

.hero-body {
  position: relative;
  z-index: 2;
  padding: 140px 80px 100px;
  max-width: 680px;
  width: 100%;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.hero-title {
  font-family: 'Playfair Display';
  font-size: clamp(3rem, 6.5vw, 5.6rem);
  font-weight: 600;
  font-style: italic;
  line-height: 1.06;
  color: #fff;
  margin-bottom: 26px;
  letter-spacing: -0.02em;
  opacity: 0;
  transform: translateX(-32px);
  transition: opacity 0.85s 0.1s cubic-bezier(0.4, 0, 0.2, 1), transform 0.85s 0.1s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-sub {
  font-family: 'Encode Sans Expsnded';
  font-size: 0.84rem;
  font-weight: 300;
  line-height: 1.8;
  color: rgba(255, 255, 255, 0.68);
  letter-spacing: 0.05em;
  max-width: 340px;
  margin-bottom: 50px;
  opacity: 0;
  transform: translateX(-32px);
  transition: opacity 0.85s 0.28s cubic-bezier(0.4, 0, 0.2, 1), transform 0.85s 0.28s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-cta {
  align-self: center;
  display: inline-flex;
  align-items: center;
  gap: 16px;
  font-family: 'Encode Sans Semi Expanded';
  font-size: 0.65rem;
  font-weight: 600;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: #1a1a18;
  background: #f0ebe2;
  border: none;
  padding: 18px 40px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  opacity: 0;
  transform: translateX(-32px);
  transition: opacity 0.85s 0.46s cubic-bezier(0.4, 0, 0.2, 1), transform 0.85s 0.46s cubic-bezier(0.4, 0, 0.2, 1),
    box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-cta::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.06);
  transform: translateX(-101%);
  transition: transform 0.38s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-visible .hero-title,
.hero-visible .hero-sub,
.hero-visible .btn-cta {
  opacity: 1;
  transform: translateX(0);
}

.btn-cta:hover::before {
  transform: translateX(0);
}

.btn-cta:hover {
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
  transform: translateX(0) translateY(-2px);
}

.btn-cta:active {
  transform: scale(0.98);
}

.arr {
  display: inline-block;
  transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-cta:hover .arr {
  transform: translateX(5px);
}

/* КАТЕГОРИИ */
.s-kategorijas {
  min-height: 100vh;
  background: #f0ebe2;
  display: flex;
  align-items: center;
  padding: 100px 80px;
}

.s-inner {
  width: 100%;
  max-width: 1100px;
}

.s-eyebrow {
  font-family: 'Encode Sans Semi Expanded';
  font-size: 0.58rem;
  font-weight: 600;
  letter-spacing: 0.28em;
  text-transform: uppercase;
  color: #1a1a18;
  opacity: 0.5;
  margin-bottom: 18px;
}

.s-title {
  font-family:'Playfair Display';
  font-size: clamp(2rem, 3.5vw, 3rem);
  font-weight: 500;
  font-style: italic;
  color: #1a1a18;
  line-height: 1.2;
  margin-bottom: 14px;
}

.s-desc {
  font-family: 'Encode Sans Semi Expanded';
  font-size: 0.8rem;
  font-weight: 300;
  color: #1a1a18;
  opacity: 0.55;
  line-height: 1.8;
  max-width: 420px;
  margin-bottom: 52px;
  letter-spacing: 0.03em;
}

.kat-cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.kat-card {
  background: #fff;
  border: 1px solid rgba(26, 26, 24, 0.08);
  padding: 36px 30px 40px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.kat-icon svg {
  width: 22px;
  height: 22px;
  stroke: currentColor;
  fill: none;
  stroke-width: 1.5;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.kat-card h3 {
  font-family: var('Playfair Display');
  font-size: 1.15rem;
  font-weight: 500;
  color: #1a1a18;
  line-height: 1.3;
}

.kat-card p {
  font-family: 'Encode Sans Semi Expanded';
  font-size: 0.71rem;
  font-weight: 300;
  color: #1a1a18;
  opacity: 0.5;
  line-height: 1.65;
  letter-spacing: 0.02em;
}

/* О НАС */
.s-about {
  min-height: 100vh;
  background: #f0ebe2;
  display: flex;
  align-items: center;
  padding: 100px 80px;
}

.about-card {
  background: #2d4a35;
  padding: 72px 64px;
  max-width: 640px;
  width: 100%;
}

.about-eyebrow {
  font-family: 'Encode Sans Semi Expanded';
  font-size: 0.58rem;
  font-weight: 600;
  letter-spacing: 0.28em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.4);
  margin-bottom: 24px;
}

.about-title {
  font-family: var('Playfair Display');
  font-size: clamp(2rem, 4.5vw, 3.8rem);
  font-weight: 600;
  font-style: italic;
  color: #fff;
  line-height: 1.1;
  margin-bottom: 4px;
}

.about-sub {
  font-family: var('Playfair Display');
  font-size: clamp(1.8rem, 4vw, 3.4rem);
  font-weight: 400;
  font-style: italic;
  color: rgba(255, 255, 255, 0.82);
  line-height: 1.1;
  margin-bottom: 36px;
}

.about-desc {
  font-family: 'Encode Sans Semi Expanded';
  font-size: 0.8rem;
  font-weight: 300;
  color: rgba(255, 255, 255, 0.58);
  line-height: 1.8;
  max-width: 380px;
  letter-spacing: 0.04em;
}

/* АДАПТИВНОСТЬ */
@media (max-width: 960px) {
  .topnav {
    padding: 0 24px;
  }
  .nav-links {
    gap: 20px;
  }
  .hero-body,
  .s-kategorijas,
  .s-about {
    padding: 100px 24px;
  }
  .kat-cards {
    grid-template-columns: 1fr;
  }
}
</style>