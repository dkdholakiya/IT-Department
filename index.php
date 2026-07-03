<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="GMIU Department of Information Technology — Faculty Portal for report submission and academic event management.">
    <title>IT Department — GMIU Faculty Portal</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/portal.css?v=3">
</head>

<body>

    <!-- ░░ Particles ░░ -->
    <div class="particles" aria-hidden="true">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- ░░ HOME SECTION ░░ -->
    <section class="home" id="home">

        <!-- Glowing Orbs -->
        <div class="orb orb-1" aria-hidden="true"></div>
        <div class="orb orb-2" aria-hidden="true"></div>
        <div class="orb orb-3" aria-hidden="true"></div>

        <div class="home-container">

            <!-- ════════ LEFT CONTENT ════════ -->
            <div class="home-left">

                <!-- University Badge -->
                <div class="badge">
                    <span class="badge-dot"></span>
                    Gyanmanjari Innovative University
                </div>

                <!-- Main Heading -->
                <h1 class="home-heading">
                    <span class="heading-line1">Department of</span>
                    <span class="heading-main">Information</span>
                    <span class="heading-accent">Technology.</span>
                </h1>

                <!-- Love-language tagline -->
                <p class="home-tagline">
                    We don't just teach syntax — we <span class="love-word">foster</span> curiosity,
                    <span class="love-word">unleash</span> innovation, and <span class="love-word">inspire</span>
                    the next generation of engineers to code with <span class="love-word">purpose</span>.
                </p>

                <!-- Inspirational Quote -->
                <div class="home-quote">
                    <p>
                        "The best way to predict the future is to invent it — and this department is where those innovations begin."
                        <cite>— GMIU IT Faculty, 2026</cite>
                    </p>
                </div>


                <!-- CTA -->
                <a href="https://gmiu.edu.in/gmiu/website/" target="_blank" class="cta-btn" id="ctaBtn">
                    <span>GMIU Website</span>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </a>

            </div>

            <!-- ════════ RIGHT PANEL ════════ -->
            <div class="home-right">

                <!-- SVG Tech Illustration as background frame -->
                <div class="hero-img-frame" aria-hidden="true">
                    <svg viewBox="0 0 500 380" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                        <defs>
                            <radialGradient id="bg-glow" cx="50%" cy="50%" r="60%">
                                <stop offset="0%" stop-color="#1a2a5e" stop-opacity="0.8" />
                                <stop offset="100%" stop-color="#060d1f" stop-opacity="0" />
                            </radialGradient>
                            <filter id="glow-filter">
                                <feGaussianBlur stdDeviation="3" result="blur" />
                                <feMerge>
                                    <feMergeNode in="blur" />
                                    <feMergeNode in="SourceGraphic" />
                                </feMerge>
                            </filter>
                        </defs>
                        <!-- Background -->
                        <rect width="500" height="380" fill="url(#bg-glow)" rx="18" />
                        <!-- Circuit lines -->
                        <g stroke="rgba(37,99,235,0.25)" stroke-width="1" fill="none">
                            <line x1="0" y1="95" x2="500" y2="95" />
                            <line x1="0" y1="190" x2="500" y2="190" />
                            <line x1="0" y1="285" x2="500" y2="285" />
                            <line x1="80" y1="0" x2="80" y2="380" />
                            <line x1="200" y1="0" x2="200" y2="380" />
                            <line x1="320" y1="0" x2="320" y2="380" />
                            <line x1="440" y1="0" x2="440" y2="380" />
                        </g>
                        <!-- Circuit nodes -->
                        <g fill="rgba(37,99,235,0.5)" filter="url(#glow-filter)">
                            <circle cx="80" cy="95" r="3" />
                            <circle cx="200" cy="95" r="3" />
                            <circle cx="320" cy="95" r="3" />
                            <circle cx="440" cy="95" r="3" />
                            <circle cx="80" cy="190" r="3" />
                            <circle cx="200" cy="190" r="3" />
                            <circle cx="320" cy="190" r="3" />
                            <circle cx="440" cy="190" r="3" />
                            <circle cx="80" cy="285" r="3" />
                            <circle cx="200" cy="285" r="3" />
                            <circle cx="320" cy="285" r="3" />
                            <circle cx="440" cy="285" r="3" />
                        </g>
                        <!-- Red glowing node clusters -->
                        <g filter="url(#glow-filter)">
                            <circle cx="200" cy="190" r="8" fill="rgba(192,57,43,0.7)" />
                            <circle cx="200" cy="190" r="14" fill="none" stroke="rgba(192,57,43,0.3)"
                                stroke-width="2" />
                            <circle cx="320" cy="95" r="6" fill="rgba(124,58,237,0.7)" />
                            <circle cx="440" cy="285" r="7" fill="rgba(6,182,212,0.6)" />
                        </g>
                        <!-- Flowing data paths -->
                        <g stroke-width="1.5" fill="none" opacity="0.6">
                            <path d="M 80 95 Q 140 95 200 95 Q 200 140 200 190" stroke="rgba(192,57,43,0.5)"
                                stroke-dasharray="4 3">
                                <animate attributeName="stroke-dashoffset" from="0" to="-28" dur="1.8s"
                                    repeatCount="indefinite" />
                            </path>
                            <path d="M 320 95 Q 380 95 440 95 Q 440 190 440 285" stroke="rgba(37,99,235,0.5)"
                                stroke-dasharray="4 3">
                                <animate attributeName="stroke-dashoffset" from="0" to="-28" dur="2.2s"
                                    repeatCount="indefinite" />
                            </path>
                            <path d="M 200 190 Q 260 190 320 190 Q 320 237 320 285" stroke="rgba(6,182,212,0.4)"
                                stroke-dasharray="4 3">
                                <animate attributeName="stroke-dashoffset" from="0" to="-28" dur="1.5s"
                                    repeatCount="indefinite" />
                            </path>
                        </g>
                        <!-- Monitor/Screen -->
                        <rect x="155" y="120" width="190" height="120" rx="8" fill="rgba(8,15,35,0.9)"
                            stroke="rgba(255,255,255,0.12)" stroke-width="1.5" />
                        <rect x="155" y="120" width="190" height="24" rx="8" fill="rgba(37,99,235,0.18)" />
                        <!-- Screen dots -->
                        <circle cx="170" cy="132" r="4" fill="#ef4444" />
                        <circle cx="182" cy="132" r="4" fill="#f59e0b" />
                        <circle cx="194" cy="132" r="4" fill="#10b981" />
                        <!-- Code lines on screen -->
                        <g font-family="monospace" font-size="9" fill-opacity="0.85">
                            <text x="165" y="158" fill="#f472b6">const</text>
                            <text x="192" y="158" fill="#38bdf8"> dept</text>
                            <text x="220" y="158" fill="#94a3b8"> = </text>
                            <text x="232" y="158" fill="#34d399">'IT';</text>
                            <text x="165" y="172" fill="#f472b6">const</text>
                            <text x="192" y="172" fill="#38bdf8"> passion</text>
                            <text x="240" y="172" fill="#94a3b8"> =</text>
                            <text x="252" y="172" fill="#fb923c"> true;</text>
                            <text x="165" y="186" fill="#475569">// We build futures</text>
                            <text x="165" y="200" fill="#fbbf24">launch</text>
                            <text x="198" y="200" fill="#94a3b8">(</text>
                            <text x="204" y="200" fill="#a5b4fc">gmiu</text>
                            <text x="228" y="200" fill="#94a3b8">);</text>
                            <text x="165" y="214" fill="#34d399">▌</text>
                        </g>
                        <!-- Stand -->
                        <rect x="231" y="240" width="38" height="6" rx="2" fill="rgba(255,255,255,0.1)" />
                        <rect x="217" y="246" width="66" height="5" rx="2" fill="rgba(255,255,255,0.08)" />
                        <!-- Decorative hexagons -->
                        <g fill="none" stroke="rgba(124,58,237,0.3)" stroke-width="1">
                            <polygon points="430,40 444,48 444,64 430,72 416,64 416,48" opacity="0.8">
                                <animateTransform attributeName="transform" type="rotate" from="0 430 56"
                                    to="360 430 56" dur="12s" repeatCount="indefinite" />
                            </polygon>
                            <polygon points="55,290 67,297 67,311 55,318 43,311 43,297" opacity="0.7">
                                <animateTransform attributeName="transform" type="rotate" from="0 55 304"
                                    to="-360 55 304" dur="9s" repeatCount="indefinite" />
                            </polygon>
                        </g>
                        <!-- Floating data packets -->
                        <g fill="rgba(192,57,43,0.8)" filter="url(#glow-filter)">
                            <rect x="95" y="148" width="6" height="6" rx="1">
                                <animate attributeName="y" values="148;108;148" dur="3s" repeatCount="indefinite" />
                                <animate attributeName="opacity" values="0;1;0" dur="3s" repeatCount="indefinite" />
                            </rect>
                            <rect x="370" y="200" width="6" height="6" rx="1">
                                <animate attributeName="y" values="200;160;200" dur="4s" repeatCount="indefinite" />
                                <animate attributeName="opacity" values="0;1;0" dur="4s" repeatCount="indefinite" />
                            </rect>
                        </g>
                    </svg>
                </div>

                <!-- Code Terminal Card (on top) -->
                <div class="terminal-card" id="terminalCard">
                    <div class="terminal-header">
                        <div class="terminal-dots">
                            <span class="tdot red"></span>
                            <span class="tdot yellow"></span>
                            <span class="tdot green"></span>
                        </div>
                        <span class="terminal-title">it_department.js</span>
                    </div>
                    <div class="terminal-body">
                        <div class="code-line"><span class="cmt">// Department of Information Technology</span></div>
                        <div class="code-line"><span class="kw">const</span> <span class="var">itDept</span> = {</div>
                        <div class="code-line">&nbsp;&nbsp;<span class="key">vision</span>: <span class="str">'Nurturing excellence & innovation'</span>,</div>
                        <div class="code-line">&nbsp;&nbsp;<span class="key">degrees</span>: [<span class="str">'B.Tech IT'</span>, <span class="str">'Diploma IT'</span>,<span class="str">'PLM'</span>],</div>
                        <div class="code-line">&nbsp;&nbsp;<span class="key">headOfDept</span>: <span class="str">'Prof. Dhaval Chandarana'</span>,</div>
                        <div class="code-line">&nbsp;&nbsp;<span class="key">labs</span>: [<span class="str">'Apple iOS Dev Lab'</span>, <span class="str">'IoT Hub'</span>],</div>
                        <div class="code-line">&nbsp;&nbsp;<span class="key">coreSubjects</span>: [</div>
                        <div class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;<span class="str">'Web Tech'</span>, <span class="str">'Data Science'</span>, <span class="str">'AI & ML'</span></div>
                        <div class="code-line">&nbsp;&nbsp;]</div>
                        <div class="code-line">};</div>
                        <div class="code-line"></div>
                        <div class="code-line"><span class="var">Develop</span>.<span class="fn">By</span> = <span class="str">'<span class="blink-cursor">DK Dholakiya...|</span>'</span>;</div>
                    </div>
                </div>

                <!-- Floating Info Pills -->
                <div class="floating-pill pill-1">
                    <span class="pill-icon">🎓</span>
                    <span>B.Tech & Diploma IT</span>
                </div>
                <div class="floating-pill pill-2">
                    <span class="pill-icon">💻</span>
                    <span>Advanced Programming Labs</span>
                </div>
                <div class="floating-pill pill-3">
                    <span class="pill-icon">🚀</span>
                    <span>Innovation & Research</span>
                </div>
                <div class="floating-pill pill-4">
                    <span class="pill-icon">🧠</span>
                    <span>Proficient Learning Method PLM</span>
                </div>

            </div>
            <!-- /home-right -->

        </div>
        <!-- /home-container -->

        <!-- Footer -->
        <!-- <footer class="home-footer">
            <p>&copy; 2026 Department of Information Technology, GMIU &nbsp;·&nbsp; Designed with <span style="color:#f87171;">♥</span> by Dev Dholakiya</p>
        </footer> -->

    </section>

    <!-- ░░ FLOATING NAV BUTTON (Bottom Right) ░░ -->
    <div class="fab-nav" id="fabNav">
        <div class="fab-menu" id="fabMenu">
            <a href="index" class="fab-link active" id="nav-home">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Home
            </a>
            <a href="faculty" class="fab-link" id="nav-faculty">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                Faculty Team
            </a>
            <a href="report" class="fab-link" id="nav-report">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                </svg>
                Report Request
            </a>
            <a href="ctlactivity" class="fab-link" id="nav-ctl">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <line x1="9" y1="3" x2="9" y2="21" />
                    <line x1="15" y1="3" x2="15" y2="21" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="3" y1="15" x2="21" y2="15" />
                </svg>
                CTL Activity
            </a>
            <a href="ctldrive" class="fab-link" id="nav-drive">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                </svg>
                Drive Scanner
            </a>
            <a href="zero-student-report" class="fab-link" id="nav-zero">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <line x1="17" y1="8" x2="23" y2="14" />
                    <line x1="23" y1="8" x2="17" y2="14" />
                </svg>
                Zero Student Report
            </a>
        </div>

        <button class="fab-btn" id="fabBtn" aria-label="Open Navigation">
            <svg class="fab-icon-menu" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"
                viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <svg class="fab-icon-close" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"
                viewBox="0 0 24 24" style="display:none;">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>


    <script>
        // ── FAB Nav Toggle ──
        const fabBtn = document.getElementById('fabBtn');
        const fabMenu = document.getElementById('fabMenu');
        const iconMenu = fabBtn.querySelector('.fab-icon-menu');
        const iconClose = fabBtn.querySelector('.fab-icon-close');

        fabBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = fabMenu.classList.toggle('open');
            fabBtn.classList.toggle('active', isOpen);
            iconMenu.style.display = isOpen ? 'none' : 'block';
            iconClose.style.display = isOpen ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (!document.getElementById('fabNav').contains(e.target)) {
                fabMenu.classList.remove('open');
                fabBtn.classList.remove('active');
                iconMenu.style.display = 'block';
                iconClose.style.display = 'none';
            }
        });


        // ── 3D Mouse-Tilt on Terminal Card ──
        const card = document.getElementById('terminalCard');
        if (card) {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width - 0.5;
                const y = (e.clientY - rect.top) / rect.height - 0.5;
                card.style.transform =
                    `perspective(1000px) rotateY(${x * 18}deg) rotateX(${-y * 12}deg) scale(1.03)`;
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateY(-10deg) rotateX(5deg)';
            });
        }


    </script>

</body>

</html>