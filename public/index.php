<?php
// Pipii.co.ke frontend: Tailwind-powered splash + AJAX dashboard for agents/admins.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipii.co.ke | Spectacular Car Showroom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Grotesk', sans-serif; }
        .glass { backdrop-filter: blur(12px); background: rgba(255,255,255,0.12); }
        .floating-card { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35); }
        .health-gate { position: fixed; inset: 0; background: #0b1224; color: #e5e7eb; display: flex; align-items: center; justify-content: center; z-index: 50; }
        .health-panel { width: min(720px, 90%); background: #111827; border: 1px solid #1f2937; border-radius: 24px; padding: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); }
        .health-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .badge { padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
        .ok { background: #10b98133; color: #34d399; border: 1px solid #10b98155; }
        .warn { background: #f59e0b33; color: #fbbf24; border: 1px solid #f59e0b55; }
        .error { background: #ef444433; color: #f87171; border: 1px solid #ef444455; }
        .health-check { background:#0f172a; padding:14px; border-radius:16px; border:1px solid #1f2937; margin-top:10px; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <div id="health-gate" class="health-gate">
        <div class="health-panel">
            <div class="health-row">
                <div>
                    <p class="text-sm text-slate-300 uppercase tracking-[0.25em]">Pipii Systems Doctor</p>
                    <h2 class="text-2xl font-bold" id="health-title">Revving up checks…</h2>
                </div>
                <span id="health-status" class="badge ok">RUNNING</span>
            </div>
            <p class="text-slate-300 text-sm" id="health-sub">Ensuring the showroom is roadworthy.</p>
            <div id="health-details"></div>
            <p class="text-xs text-slate-400 mt-3">Need a full report? <a href="doctor.php" class="text-emerald-300 underline">Open the doctor page</a>.</p>
        </div>
    </div>
    <header class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-pink-500/20 via-blue-500/10 to-emerald-400/20 blur-3xl scale-125"></div>
        <div class="max-w-6xl mx-auto px-6 pt-10 pb-16 relative z-10">
            <nav class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-pink-500 to-purple-500 flex items-center justify-center shadow-xl">
                        <span class="text-2xl font-black">P</span>
                    </div>
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-pink-200">Pipii.co.ke</p>
                        <p class="font-semibold text-xl">Spectacular Auto Marketplace</p>
                    </div>
                </div>
                <div class="hidden md:flex space-x-4">
                    <a href="#listings" class="px-4 py-2 rounded-full bg-white/10 border border-white/20 hover:bg-white/20 transition">Showroom</a>
                    <a href="#post" class="px-4 py-2 rounded-full bg-gradient-to-r from-emerald-500 to-cyan-500 text-slate-900 font-bold shadow-lg">Create Listing</a>
                </div>
            </nav>
            <div class="grid md:grid-cols-2 gap-10 mt-12 items-center">
                <div class="space-y-6">
                    <p class="uppercase tracking-[0.4em] text-sm text-emerald-200">Admin-first. Agent-ready.</p>
                    <h1 class="text-4xl md:text-5xl font-black leading-tight">Show, Sell, and Renew the hottest rides in Kenya.</h1>
                    <p class="text-slate-200 text-lg">Admins publish instantly-visible drops. Elite agents unlock 30 slots for KES 15,000 yearly versus KES 1,000 per single listing. Every spotlight runs for one month and renews with a tap.</p>
                    <div class="flex flex-wrap gap-3">
                        <button id="cta-listings" class="px-6 py-3 rounded-full bg-gradient-to-r from-pink-500 to-orange-400 text-slate-900 font-semibold shadow-lg">Browse Cars</button>
                        <button id="cta-agent" class="px-6 py-3 rounded-full border border-white/30 hover:bg-white/10 transition">Become an Agent</button>
                    </div>
                    <div class="flex items-center space-x-4 text-sm text-slate-200">
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-100 border border-emerald-400/50">1-month spotlight</span>
                        <span class="px-3 py-1 rounded-full bg-pink-500/20 text-pink-100 border border-pink-400/40">Renew instantly</span>
                        <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-100 border border-indigo-400/40">Tailwind + AJAX</span>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute -inset-6 bg-gradient-to-tr from-pink-500/30 via-cyan-500/30 to-purple-500/40 blur-2xl"></div>
                    <div class="relative glass rounded-3xl p-6 border border-white/10 floating-card">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="rounded-2xl overflow-hidden border border-white/10">
                                <div class="bg-slate-900/70 p-4 flex items-center justify-between">
                                    <h3 class="font-semibold">Featured Slides</h3>
                                    <span class="text-xs text-slate-300">Pipii Spotlight</span>
                                </div>
                                <div class="relative">
                                    <div class="w-full h-64" id="hero-slider"></div>
                                </div>
                            </div>
                            <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                                <h4 class="font-semibold mb-3">Agent Advantage</h4>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-300/30">
                                        <p class="font-bold">KES 15,000/yr</p>
                                        <p>List up to 30 prime cars with renewals.</p>
                                    </div>
                                    <div class="p-3 rounded-xl bg-pink-500/10 border border-pink-300/30">
                                        <p class="font-bold">KES 1,000</p>
                                        <p>Single premium highlight for independents.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 pb-20 space-y-16">
        <section id="listings" class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.25em] text-emerald-200">Live Showroom</p>
                    <h2 class="text-3xl font-bold">Curated listings from admin & elite agents</h2>
                </div>
                <button class="px-4 py-2 rounded-full bg-white/10 border border-white/20 hover:bg-white/20 text-sm" id="refresh-listings">Refresh</button>
            </div>
            <div id="listings-grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6"></div>
            <p id="listings-empty" class="text-center text-slate-400 hidden">No listings yet. Admin & agents will drop cars here.</p>
        </section>

        <section id="post" class="space-y-10">
            <div class="grid md:grid-cols-2 gap-8">
                <div class="glass border border-white/10 rounded-3xl p-6 floating-card">
                    <p class="text-sm uppercase tracking-[0.25em] text-pink-200">Session</p>
                    <h3 class="text-2xl font-bold mb-4">Login as Admin or Agent</h3>
                    <form id="login-form" class="space-y-4">
                        <div>
                            <label class="block text-sm mb-1">Email</label>
                            <input name="email" type="email" required class="w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-400" placeholder="you@pipii.co.ke">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Password</label>
                            <input name="password" type="password" required class="w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-400">
                        </div>
                        <button class="w-full py-3 rounded-full bg-gradient-to-r from-pink-500 to-orange-400 text-slate-900 font-semibold shadow-lg">Login</button>
                    </form>
                    <div class="mt-4 text-sm text-slate-300" id="login-status">Use admin-issued credentials. Agents are created by admins.</div>
                </div>

                <div class="glass border border-white/10 rounded-3xl p-6 floating-card">
                    <p class="text-sm uppercase tracking-[0.25em] text-emerald-200">Post</p>
                    <h3 class="text-2xl font-bold mb-4">Create a listing (1-month spotlight)</h3>
                    <form id="listing-form" class="space-y-4">
                        <div>
                            <label class="block text-sm mb-1">Title</label>
                            <input name="title" required class="w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="Range Rover Vogue 2023">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Description</label>
                            <textarea name="description" required class="w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400" rows="3" placeholder="Luxury SUV, black on black, panoramic roof..."></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm mb-1">Price (KES)</label>
                                <input name="price" type="number" min="0" step="0.01" required class="w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="8500000">
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Location</label>
                                <input name="location" required class="w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="Karen, Nairobi">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Image URL</label>
                            <input name="image_url" class="w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="https://images.unsplash.com/...">
                        </div>
                        <button class="w-full py-3 rounded-full bg-gradient-to-r from-emerald-500 to-cyan-400 text-slate-900 font-semibold shadow-lg">Post Spotlight</button>
                    </form>
                    <p class="text-sm text-slate-300 mt-4">Agents with active membership can post up to 30 active listings. Admins post unlimited features for everyone to view.</p>
                </div>
            </div>

            <div class="glass border border-white/10 rounded-3xl p-6 floating-card">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-indigo-200">Renew</p>
                        <h3 class="text-2xl font-bold">Renew a listing for 1 extra month</h3>
                        <p class="text-slate-300 text-sm">Admins can renew any listing. Agents can renew their own spots instantly.</p>
                    </div>
                    <form id="renew-form" class="flex flex-col md:flex-row gap-3 items-start">
                        <input type="number" name="listing_id" min="1" required class="w-full md:w-48 rounded-xl bg-white/10 border border-white/20 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-400" placeholder="Listing ID">
                        <button class="px-6 py-3 rounded-full bg-gradient-to-r from-indigo-500 to-blue-400 text-slate-900 font-semibold shadow-lg">Renew Now</button>
                    </form>
                </div>
                <div id="renew-status" class="text-sm text-slate-200 mt-3"></div>
            </div>
        </section>
    </main>

    <footer class="bg-slate-900/80 border-t border-white/10 py-8">
        <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-sm text-slate-300">
            <div>
                <p class="font-semibold">Pipii.co.ke</p>
                <p>Curated car-selling platform with admin oversight and agent perks.</p>
            </div>
            <div class="flex gap-3">
                <span class="px-3 py-1 rounded-full bg-white/10 border border-white/20">Tailwind UI</span>
                <span class="px-3 py-1 rounded-full bg-white/10 border border-white/20">AJAX API</span>
                <span class="px-3 py-1 rounded-full bg-white/10 border border-white/20">MySQL Ready</span>
            </div>
        </div>
    </footer>

    <script>
        const healthGate = document.getElementById('health-gate');
        const healthStatusEl = document.getElementById('health-status');
        const healthTitleEl = document.getElementById('health-title');
        const healthSubEl = document.getElementById('health-sub');
        const healthDetailsEl = document.getElementById('health-details');

        const heroSlider = document.getElementById('hero-slider');
        const slides = [
            'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1503736334956-bbad939e9335?auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?auto=format&fit=crop&w=1000&q=80'
        ];
        let slideIndex = 0;
        function renderSlide() {
            heroSlider.style.backgroundImage = `url(${slides[slideIndex]})`;
            heroSlider.className = 'bg-cover bg-center h-64 transition-all duration-700';
            slideIndex = (slideIndex + 1) % slides.length;
        }
        renderSlide();
        setInterval(renderSlide, 2800);

        function badgeClass(status) {
            if (status === 'ok') return 'badge ok';
            if (status === 'warn') return 'badge warn';
            return 'badge error';
        }

        function renderHealth(health) {
            healthStatusEl.textContent = (health.status || 'error').toUpperCase();
            healthStatusEl.className = badgeClass(health.status);
            healthTitleEl.textContent = health.status === 'ok' ? 'Pits are clear. Let’s roll.' : (health.status === 'warn' ? 'Yellow flag. You can cruise with caution.' : 'Red flag. We’re in the pit lane.');
            healthSubEl.textContent = `Environment: ${health.environment} • Lap time ${health.duration_ms}ms`;
            healthDetailsEl.innerHTML = '';
            (health.checks || []).forEach(check => {
                const div = document.createElement('div');
                div.className = 'health-check';
                div.innerHTML = `
                    <div class="health-row">
                        <strong>${check.name}</strong>
                        <span class="${badgeClass(check.status)}">${check.status.toUpperCase()}</span>
                    </div>
                    <p class="text-sm text-slate-200">User: ${check.user_message}</p>
                    <p class="text-sm text-slate-400">Dev: ${check.dev_message}</p>
                `;
                healthDetailsEl.appendChild(div);
            });
        }

        async function runHealthGate() {
            let health;
            try {
                const res = await fetch('../api/health.php', { cache: 'no-store' });
                health = await res.json();
            } catch (e) {
                health = {
                    status: 'error',
                    environment: 'unknown',
                    duration_ms: 0,
                    checks: [{
                        name: 'connectivity',
                        status: 'error',
                        user_message: 'Diagnostics stalled. The cockpit lost connection.',
                        dev_message: 'Health endpoint unreachable: ' + e.message,
                    }],
                };
            }
            renderHealth(health);
            if (health.status === 'ok' || health.status === 'warn') {
                setTimeout(() => {
                    healthGate.style.display = 'none';
                }, 3000);
            }
        }

        runHealthGate();

        const session = {
            email: localStorage.getItem('pipii_email'),
            token: localStorage.getItem('pipii_token'),
        };

        function updateLoginStatus(text, success = false) {
            const el = document.getElementById('login-status');
            el.textContent = text;
            el.className = success ? 'mt-4 text-sm text-emerald-200' : 'mt-4 text-sm text-slate-300';
        }

        function authHeaders() {
            if (!session.email || !session.token) return {};
            return {
                'X-User-Email': session.email,
                'X-User-Token': session.token,
            };
        }

        async function fetchListings() {
            const grid = document.getElementById('listings-grid');
            const empty = document.getElementById('listings-empty');
            grid.innerHTML = '<p class="text-slate-300">Loading...</p>';
            try {
                const res = await fetch('../api/listings.php');
                const body = await res.json();
                const listings = body.data || [];
                grid.innerHTML = '';
                if (!listings.length) {
                    empty.classList.remove('hidden');
                    return;
                }
                empty.classList.add('hidden');
                listings.forEach((listing) => {
                    const card = document.createElement('div');
                    card.className = 'rounded-2xl overflow-hidden border border-white/10 bg-white/5 floating-card';
                    card.innerHTML = `
                        <div class="h-48 bg-cover bg-center" style="background-image:url('${listing.image_url || 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=800&q=80'}')"></div>
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold">${listing.title}</h3>
                                <span class="px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-200 text-xs">KES ${Number(listing.price).toLocaleString()}</span>
                            </div>
                            <p class="text-sm text-slate-200">${listing.description}</p>
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span>Seller: ${listing.seller_name}</span>
                                <span>${listing.location}</span>
                            </div>
                            <p class="text-xs text-slate-400">Expires: ${new Date(listing.expires_at).toLocaleDateString()}</p>
                        </div>`;
                    grid.appendChild(card);
                });
            } catch (e) {
                grid.innerHTML = '<p class="text-red-300">Failed to load listings.</p>';
            }
        }

        document.getElementById('refresh-listings').addEventListener('click', fetchListings);
        document.getElementById('cta-listings').addEventListener('click', () => document.getElementById('listings').scrollIntoView({behavior:'smooth'}));
        document.getElementById('cta-agent').addEventListener('click', () => document.getElementById('post').scrollIntoView({behavior:'smooth'}));

        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const payload = {
                email: form.email.value,
                password: form.password.value,
            };
            const res = await fetch('../api/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const body = await res.json();
            if (res.ok) {
                session.email = body.email;
                session.token = body.token;
                localStorage.setItem('pipii_email', body.email);
                localStorage.setItem('pipii_token', body.token);
                updateLoginStatus(`Signed in as ${body.name} (${body.role})`, true);
            } else {
                updateLoginStatus(body.error || 'Login failed');
            }
        });

        document.getElementById('listing-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!session.email || !session.token) {
                updateLoginStatus('Please login before posting');
                return;
            }
            const formData = new FormData(e.target);
            const res = await fetch('../api/create_listing.php', {
                method: 'POST',
                headers: { ...authHeaders() },
                body: formData,
            });
            const body = await res.json();
            if (res.ok) {
                e.target.reset();
                fetchListings();
                updateLoginStatus(body.message || 'Listing created', true);
            } else {
                updateLoginStatus(body.error || 'Failed to create listing');
            }
        });

        document.getElementById('renew-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!session.email || !session.token) {
                updateLoginStatus('Please login before renewing');
                return;
            }
            const listingId = e.target.listing_id.value;
            const res = await fetch('../api/renew_listing.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', ...authHeaders() },
                body: JSON.stringify({ listing_id: listingId }),
            });
            const body = await res.json();
            const status = document.getElementById('renew-status');
            if (res.ok) {
                status.textContent = body.message + ' (expires ' + body.expires_at + ')';
                status.className = 'text-sm text-emerald-200 mt-3';
                fetchListings();
            } else {
                status.textContent = body.error || 'Renewal failed';
                status.className = 'text-sm text-red-300 mt-3';
            }
        });

        fetchListings();
    </script>
</body>
</html>
