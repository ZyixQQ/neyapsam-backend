<!DOCTYPE html>
<html lang="tr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ne Yapsam?</title>
    <meta
      name="description"
      content="Kararsiz kaldiginda topluluk verisiyle hizli karar veren landing deneyimi."
    />
    <link
      rel="icon"
      href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='20' fill='%23F97316'/%3E%3Ctext x='50%25' y='54%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial' font-size='28' font-weight='700' fill='white'%3EN%3C/text%3E%3C/svg%3E"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Space+Grotesk:wght@500;700&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              brand: {
                DEFAULT: "#F97316",
                deep: "#c2410c",
                soft: "#fff4ed",
                text: "#1a1a1a",
                border: "#fed7aa",
              },
            },
            fontFamily: {
              display: ["Space Grotesk", "sans-serif"],
              body: ["Manrope", "sans-serif"],
            },
            boxShadow: {
              glow: "0 35px 90px rgba(249, 115, 22, 0.18)",
            },
            backgroundImage: {
              mesh: "radial-gradient(circle at top left, rgba(249, 115, 22, 0.18), transparent 30%), radial-gradient(circle at bottom right, rgba(251, 191, 36, 0.18), transparent 28%)",
            },
          },
        },
      };
      window.NEYAPSAM_API_BASE = "/api/v1";
    </script>
    <style>
      html {
        scroll-behavior: smooth;
      }

      body {
        background:
          radial-gradient(circle at top left, rgba(249, 115, 22, 0.16), transparent 22%),
          linear-gradient(180deg, #fffaf7 0%, #ffffff 28%, #ffffff 100%);
      }
    </style>
  </head>
  <body class="bg-white font-body text-brand-text antialiased">
    <div class="absolute inset-x-0 top-0 -z-10 h-[36rem] bg-mesh"></div>

    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <header class="flex items-center justify-between py-6">
        <a href="#hero" class="font-display text-2xl font-bold tracking-tight text-brand-deep">ne yapsam?</a>
        <nav class="hidden items-center gap-6 text-sm font-semibold text-zinc-700 md:flex">
          <a href="#how-it-works" class="transition hover:text-brand">Nasil Calisir</a>
          <a href="#categories" class="transition hover:text-brand">Kategoriler</a>
          <a href="#preview" class="transition hover:text-brand">App Preview</a>
        </nav>
        <a
          href="#categories"
          class="rounded-full border border-brand px-4 py-2 text-sm font-semibold text-brand transition hover:bg-brand hover:text-white"
        >
          Kesfet
        </a>
      </header>

      <section
        id="hero"
        class="relative grid gap-10 rounded-[2rem] border border-brand-border bg-white/90 px-6 py-8 shadow-glow backdrop-blur lg:grid-cols-[1.15fr_0.85fr] lg:px-10 lg:py-12"
      >
        <div class="space-y-8">
          <div class="inline-flex items-center gap-2 rounded-full bg-brand-soft px-4 py-2 text-sm font-semibold text-brand-deep">
            Topluluktan beslenen hizli karar motoru
          </div>
          <div class="space-y-5">
            <h1 class="max-w-2xl font-display text-5xl font-bold leading-tight sm:text-6xl">
              Kararsiz kaldiginda ekrana degil, net cevaba git.
            </h1>
            <p class="max-w-xl text-lg leading-8 text-zinc-600">
              Ne Yapsam? kategori secimi, alt baslik daraltma ve topluluk oylarini tek akista toplar.
              Dakikalarca gezmek yerine saniyeler icinde karar verirsin.
            </p>
          </div>
          <div class="flex flex-col gap-3 sm:flex-row">
            <a
              href="#preview"
              class="rounded-full bg-brand px-6 py-3 text-center text-sm font-semibold text-white transition hover:bg-brand-deep"
            >
              Ekranlari Incele
            </a>
            <a
              href="http://localhost/admin"
              class="rounded-full border border-zinc-200 px-6 py-3 text-center text-sm font-semibold text-zinc-700 transition hover:border-brand hover:text-brand"
            >
              Admin Paneli
            </a>
          </div>
          <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-3xl border border-zinc-100 bg-zinc-50 p-4">
              <div class="font-display text-3xl font-bold text-brand">4+</div>
              <div class="mt-2 text-sm text-zinc-500">Canli kategori akisi</div>
            </div>
            <div class="rounded-3xl border border-zinc-100 bg-zinc-50 p-4">
              <div class="font-display text-3xl font-bold text-brand">24/7</div>
              <div class="mt-2 text-sm text-zinc-500">Topluluk katkisi</div>
            </div>
            <div class="rounded-3xl border border-zinc-100 bg-zinc-50 p-4">
              <div class="font-display text-3xl font-bold text-brand">&lt;30 sn</div>
              <div class="mt-2 text-sm text-zinc-500">Karar suresi</div>
            </div>
          </div>
        </div>

        <div class="relative flex items-center justify-center">
          <div class="absolute -top-6 right-6 h-20 w-20 rounded-full bg-brand/15 blur-2xl"></div>
          <div class="absolute -bottom-10 left-2 h-24 w-24 rounded-full bg-amber-300/20 blur-3xl"></div>
          <div class="w-full max-w-md rounded-[2rem] border border-brand-border bg-brand-text p-4 text-white shadow-2xl">
            <div class="rounded-[1.6rem] bg-brand-soft p-4 text-brand-text">
              <div class="mb-4 flex items-center justify-between text-sm font-semibold text-brand-deep">
                <span>Bugun ne yapsam?</span>
                <span>Canli API</span>
              </div>
              <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-brand-border bg-white px-4 py-5 shadow-sm">
                  <div class="text-xs uppercase tracking-[0.2em] text-zinc-400">Kategori</div>
                  <div class="mt-2 font-display text-xl font-bold">Yemek</div>
                </div>
                <div class="rounded-2xl border border-brand-border bg-white px-4 py-5 shadow-sm">
                  <div class="text-xs uppercase tracking-[0.2em] text-zinc-400">Kategori</div>
                  <div class="mt-2 font-display text-xl font-bold">Gezi</div>
                </div>
                <div class="rounded-2xl border border-brand-border bg-white px-4 py-5 shadow-sm">
                  <div class="text-xs uppercase tracking-[0.2em] text-zinc-400">Kategori</div>
                  <div class="mt-2 font-display text-xl font-bold">Etkinlik</div>
                </div>
                <div class="rounded-2xl border border-brand-border bg-white px-4 py-5 shadow-sm">
                  <div class="text-xs uppercase tracking-[0.2em] text-zinc-400">Kategori</div>
                  <div class="mt-2 font-display text-xl font-bold">Hediye</div>
                </div>
              </div>
            </div>
            <div class="mt-4 rounded-[1.4rem] border border-white/10 bg-white/5 p-4">
              <div class="text-sm text-orange-100">Topluluk sinyali</div>
              <div class="mt-3 flex items-end justify-between gap-4">
                <div>
                  <div class="font-display text-4xl font-bold">+142</div>
                  <div class="text-sm text-zinc-300">Aktif oy dengesi</div>
                </div>
                <div class="rounded-full bg-white/10 px-3 py-2 text-sm font-semibold">Gercek zamanli</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="how-it-works" class="py-24">
        <div class="mb-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand">Nasil Calisir</p>
            <h2 class="mt-3 font-display text-4xl font-bold">Kalabalik veri yerine duzgun akis</h2>
          </div>
          <p class="max-w-2xl text-base leading-7 text-zinc-600">
            Secim, filtreleme, puanlama ve sonuca gecis. Landing tarafinda da ayni minimal mantigi
            koruyoruz: net bloklar, fark edilir tipografi ve hizli okunurluk.
          </p>
        </div>

        <div class="grid gap-5 lg:grid-cols-3">
          <article class="rounded-[1.75rem] border border-zinc-100 bg-zinc-50 p-6 transition hover:-translate-y-1 hover:border-brand-border hover:bg-brand-soft">
            <div class="font-display text-5xl font-bold text-brand/30">01</div>
            <h3 class="mt-6 font-display text-2xl font-bold">Kategori sec</h3>
            <p class="mt-3 text-base leading-7 text-zinc-600">
              Yemek, gezi, hediye ya da gunluk kararsizlik. Baslangic noktasini tek tikla sec.
            </p>
          </article>
          <article class="rounded-[1.75rem] border border-zinc-100 bg-zinc-50 p-6 transition hover:-translate-y-1 hover:border-brand-border hover:bg-brand-soft">
            <div class="font-display text-5xl font-bold text-brand/30">02</div>
            <h3 class="mt-6 font-display text-2xl font-bold">Alt basligi daralt</h3>
            <p class="mt-3 text-base leading-7 text-zinc-600">
              Toplulugun hangi senaryoda ne onerdigini temiz kartlarla gor, secenekleri daha hizli ele.
            </p>
          </article>
          <article class="rounded-[1.75rem] border border-zinc-100 bg-zinc-50 p-6 transition hover:-translate-y-1 hover:border-brand-border hover:bg-brand-soft">
            <div class="font-display text-5xl font-bold text-brand/30">03</div>
            <h3 class="mt-6 font-display text-2xl font-bold">En iyi cevabi yakala</h3>
            <p class="mt-3 text-base leading-7 text-zinc-600">
              Oylar, raporlar ve taze suggestion verisiyle daha guvenilir sonuca aninda ulas.
            </p>
          </article>
        </div>
      </section>

      <section id="categories" class="py-24">
        <div class="mb-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand">Kategoriler</p>
            <h2 class="mt-3 font-display text-4xl font-bold">Canli backend verisiyle beslenen kartlar</h2>
          </div>
          <p class="max-w-2xl text-base leading-7 text-zinc-600">
            Bu bolum browser tarafinda direkt API'den besleniyor. Backend cevap vermezse kontrollu fallback
            kartlara dusuyor.
          </p>
        </div>

        <div id="category-grid" class="grid gap-5 md:grid-cols-2 xl:grid-cols-4"></div>
      </section>

      <section class="grid gap-6 py-24 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="rounded-[2rem] bg-brand px-8 py-10 text-white shadow-glow">
          <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-100">Istatistik</p>
          <h2 class="mt-4 font-display text-4xl font-bold">Gercek sinyali one cikar</h2>
          <p class="mt-4 max-w-md text-base leading-7 text-orange-50">
            Oylama dengesi, raporlama akisleri ve taze suggestion verisi tek yerde toplaniyor.
          </p>
        </div>
        <div class="grid gap-5 sm:grid-cols-3">
          <article class="rounded-[1.75rem] border border-zinc-100 bg-white p-6 shadow-sm">
            <h3 class="font-display text-2xl font-bold">Top suggestion</h3>
            <p class="mt-3 text-base leading-7 text-zinc-600">Yuksek oy alan oneriler daha gorunur hale gelir.</p>
          </article>
          <article class="rounded-[1.75rem] border border-zinc-100 bg-white p-6 shadow-sm">
            <h3 class="font-display text-2xl font-bold">Report sistemi</h3>
            <p class="mt-3 text-base leading-7 text-zinc-600">Dusuk kaliteli icerik hizli sekilde ayiklanir.</p>
          </article>
          <article class="rounded-[1.75rem] border border-zinc-100 bg-white p-6 shadow-sm">
            <h3 class="font-display text-2xl font-bold">Ayni API</h3>
            <p class="mt-3 text-base leading-7 text-zinc-600">Landing ve mobil ayni backend katmanini kullanir.</p>
          </article>
        </div>
      </section>

      <section id="preview" class="py-24">
        <div class="mb-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-brand">App Preview</p>
            <h2 class="mt-3 font-display text-4xl font-bold">Uc ekranlik net akis</h2>
          </div>
          <p class="max-w-2xl text-base leading-7 text-zinc-600">
            Root referanslarindan gelen uc ekran, landing anlatiminin merkezinde. Mobil phase ile ayni dil
            korunuyor.
          </p>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
          <article class="rounded-[2rem] border border-zinc-100 bg-white p-4 shadow-lg">
            <div class="overflow-hidden rounded-[1.7rem] border border-zinc-200 bg-zinc-100">
              <img src="/app_1.png" alt="Ana sayfa kategori grid gorunumu" class="h-auto w-full object-cover" />
            </div>
            <div class="px-2 pb-3 pt-5">
              <h3 class="font-display text-2xl font-bold">Ana sayfa</h3>
              <p class="mt-2 text-base leading-7 text-zinc-600">Kategori grid ile karar akisina hizli giris.</p>
            </div>
          </article>
          <article class="rounded-[2rem] border border-brand-border bg-brand-soft p-4 shadow-lg">
            <div class="overflow-hidden rounded-[1.7rem] border border-zinc-200 bg-zinc-100">
              <img src="/app_2.png" alt="Alt kategori secim ekrani" class="h-auto w-full object-cover" />
            </div>
            <div class="px-2 pb-3 pt-5">
              <h3 class="font-display text-2xl font-bold">Alt kategori secimi</h3>
              <p class="mt-2 text-base leading-7 text-zinc-600">Daralt, karsilastir ve karar alanini netlestir.</p>
            </div>
          </article>
          <article class="rounded-[2rem] border border-zinc-100 bg-white p-4 shadow-lg">
            <div class="overflow-hidden rounded-[1.7rem] border border-zinc-200 bg-zinc-100">
              <img src="/app_3.png" alt="Sonuc ve oneri listesi ekrani" class="h-auto w-full object-cover" />
            </div>
            <div class="px-2 pb-3 pt-5">
              <h3 class="font-display text-2xl font-bold">Sonuc listesi</h3>
              <p class="mt-2 text-base leading-7 text-zinc-600">Yuksek oy alan onerileri hizli sirala ve filtrele.</p>
            </div>
          </article>
        </div>
      </section>

      <footer class="border-t border-zinc-100 py-10">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <div class="font-display text-2xl font-bold text-brand-deep">ne yapsam?</div>
            <p class="mt-2 text-sm text-zinc-500">Kararsizligi temiz akisa ceviren topluluk urunu.</p>
          </div>
          <div class="flex flex-wrap gap-3 text-sm font-semibold text-zinc-600">
            <a href="/api/v1/categories" class="rounded-full bg-zinc-100 px-4 py-2 transition hover:text-brand">API</a>
            <a href="http://localhost:8080" class="rounded-full bg-zinc-100 px-4 py-2 transition hover:text-brand">phpMyAdmin</a>
          </div>
        </div>
      </footer>
    </main>

    <script>
      const fallbackCategories = [
        { name: "Yemek", badge: "YMK", subcategories_count: 4, accent: true },
        { name: "Gezi", badge: "GZI", subcategories_count: 4 },
        { name: "Etkinlik", badge: "ETK", subcategories_count: 4 },
        { name: "Hediye", badge: "HDY", subcategories_count: 4 },
      ];

      function categoryCard(category, index, loadingLabel) {
        const accent = index === 0;
        const wrapper = document.createElement("article");
        wrapper.className =
          "group rounded-[1.8rem] border p-6 transition hover:-translate-y-1 " +
          (accent
            ? "border-brand-border bg-brand-soft shadow-glow"
            : "border-zinc-100 bg-white");
        wrapper.innerHTML = `
          <div class="flex items-start justify-between gap-4">
            <div class="rounded-full bg-brand-text px-3 py-2 text-sm font-bold tracking-[0.2em] text-white">${category.emoji || category.badge || "NYS"}</div>
            <div class="rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">
              ${category.subcategories_count || 0} alt baslik
            </div>
          </div>
          <h3 class="mt-8 font-display text-3xl font-bold">${category.name}</h3>
          <p class="mt-3 text-base leading-7 text-zinc-600">
            Toplulugun hizli karar akisini bu kategori icinde baslat. Oylama, suggestion ve alt baslik yapisi hazir.
          </p>
          <div class="mt-6 text-sm font-semibold text-brand">${loadingLabel}</div>
        `;
        return wrapper;
      }

      async function loadCategories() {
        const grid = document.getElementById("category-grid");
        if (!grid) return;

        grid.innerHTML = "";
        fallbackCategories.forEach((category, index) => {
          grid.appendChild(categoryCard(category, index, "Yedek kart hazir"));
        });

        try {
          const response = await fetch(`${window.NEYAPSAM_API_BASE}/categories`, {
            headers: { Accept: "application/json" },
          });

          if (!response.ok) {
            throw new Error("Category request failed");
          }

          const payload = await response.json();
          const categories = Array.isArray(payload.data) ? payload.data.slice(0, 4) : [];
          if (!categories.length) {
            return;
          }

          grid.innerHTML = "";
          categories.forEach((category, index) => {
            grid.appendChild(categoryCard(category, index, "API senkronu hazir"));
          });
        } catch (error) {
          console.warn("Category API fallback used:", error);
        }
      }

      loadCategories();
    </script>
  </body>
</html>
