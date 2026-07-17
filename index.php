<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="GMIU Department of Information Technology — Faculty Portal for report submission and academic event management.">
    <title>CE & IT Department — GMIU Faculty Portal</title>
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

                <!-- Department Selector Toggle -->
                <div class="zs-segment-control">
                    <button type="button" class="segment-btn active" id="dept-it-btn" data-dept="Information Technology">Information Technology</button>
                    <button type="button" class="segment-btn" id="dept-ce-btn" data-dept="Computer Engineering">Computer Engineering</button>
                </div>

                <!-- University Badge -->
                <!-- <div class="badge">
                    <span class="badge-dot"></span>
                    Gyanmanjari Innovative University
                </div> -->

                <!-- Main Heading -->
                <h1 class="home-heading">
                    <span class="heading-line1">Department of</span>
                    <span class="heading-main" id="headingMain">Information</span>
                    <span class="heading-accent" id="headingAccent">Technology.</span>
                </h1>

                <!-- Love-language tagline -->
                <p class="home-tagline" id="homeTagline">
                    We don't just teach syntax — we <span class="love-word">foster</span> curiosity,
                    <span class="love-word">unleash</span> innovation, and <span class="love-word">inspire</span>
                    the next generation of engineers to code with <span class="love-word">purpose</span>.
                </p>

                <!-- Inspirational Quote -->
                <div class="home-quote" id="homeQuote">
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
                        <span class="terminal-title" id="terminalTitle">it_department.js</span>
                    </div>
                    <div class="terminal-body" id="terminalBody">
                        <div class="code-line"><span class="cmt">// Department of Information Technology</span></div>
                        <div class="code-line"><span class="kw">const</span> <span class="var">itDept</span> = {</div>
                        <div class="code-line">&nbsp;&nbsp;<span class="key">vision</span>: <span class="str">'Nurturing excellence & innovation'</span>,</div>
                        <div class="code-line">&nbsp;&nbsp;<span class="key">degrees</span>: [<span class="str">'B.Tech IT'</span>, <span class="str">'Diploma IT'</span>,<span class="str">'PLM'</span>],</div>
                        <div class="code-line">&nbsp;&nbsp;<span class="key">headOfDept</span>: <span class="str">'Prof. Dhaval Chandarana'</span>,</div>
                        <div class="code-line">&nbsp;&nbsp;<span class="key">inchargeHod</span>: <span class="str">'Prof. Shwetaba Chauhan'</span>,</div>
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
                <div class="floating-pill pill-1" id="pill1">
                    <span class="pill-icon">🎓</span>
                    <span>B.Tech & Diploma IT</span>
                </div>
                <div class="floating-pill pill-2" id="pill2">
                    <span class="pill-icon">💻</span>
                    <span>Advanced Programming Labs</span>
                </div>
                <div class="floating-pill pill-3" id="pill3">
                    <span class="pill-icon">🚀</span>
                    <span>Innovation & Research</span>
                </div>
                <div class="floating-pill pill-4" id="pill4">
                    <span class="pill-icon">🧠</span>
                    <span>Proficient Learning Method PLM</span>
                </div>

            </div>
            <!-- /home-right -->

        </div>
        <!-- /home-container -->

        <!-- Footer -->
        

    </section>

    <!-- ░░ FLOATING NAV BUTTON (Bottom Right) ░░ -->
    <?php 
    $active_page = 'home';
    include 'fab-nav.php'; 
    ?>

    <script>
        // ── Department Selection Toggle ──
        const itBtn = document.getElementById('dept-it-btn');
        const ceBtn = document.getElementById('dept-ce-btn');

        const headingMain = document.getElementById('headingMain');
        const headingAccent = document.getElementById('headingAccent');
        const homeTagline = document.getElementById('homeTagline');
        const homeQuote = document.getElementById('homeQuote');
        const terminalTitle = document.getElementById('terminalTitle');
        const terminalBody = document.getElementById('terminalBody');
        const pill1 = document.getElementById('pill1');
        const pill2 = document.getElementById('pill2');
        const pill3 = document.getElementById('pill3');
        const pill4 = document.getElementById('pill4');

        const itContent = {
            headingMain: "Information",
            headingAccent: "Technology.",
            tagline: `We don't just teach syntax — we <span class="love-word">foster</span> curiosity, <span class="love-word">unleash</span> innovation, and <span class="love-word">inspire</span> the next generation of engineers to code with <span class="love-word">purpose</span>.`,
            quote: `<p>"The best way to predict the future is to invent it — and this department is where those innovations begin."<cite>— GMIU IT Faculty, 2026</cite></p>`,
            terminalTitle: "it_department.js",
            terminalBody: `
                <div class="code-line"><span class="cmt">// Department of Information Technology</span></div>
                <div class="code-line"><span class="kw">const</span> <span class="var">itDept</span> = {</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">vision</span>: <span class="str">'Nurturing excellence & innovation'</span>,</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">degrees</span>: [<span class="str">'B.Tech IT'</span>, <span class="str">'Diploma IT'</span>,<span class="str">'PLM'</span>],</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">headOfDept</span>: <span class="str">'Prof. Dhaval Chandarana'</span>,</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">inchargeHod</span>: <span class="str">'Prof. Shwetaba Chauhan'</span>,</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">labs</span>: [<span class="str">'Apple iOS Dev Lab'</span>, <span class="str">'IoT Hub'</span>],</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">coreSubjects</span>: [</div>
                <div class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;<span class="str">'Web Tech'</span>, <span class="str">'Data Science'</span>, <span class="str">'AI & ML'</span></div>
                <div class="code-line">&nbsp;&nbsp;]</div>
                <div class="code-line">};</div>
                <div class="code-line"></div>
                <div class="code-line"><span class="var">Develop</span>.<span class="fn">By</span> = <span class="str">'<span class="blink-cursor">DK Dholakiya...|</span>'</span>;
            `,
            pill1: `<span class="pill-icon">🎓</span><span>B.Tech & Diploma IT</span>`,
            pill2: `<span class="pill-icon">💻</span><span>Advanced Programming Labs</span>`,
            pill3: `<span class="pill-icon">🚀</span><span>Innovation & Research</span>`,
            pill4: `<span class="pill-icon">🧠</span><span>Proficient Learning Method PLM</span>`
        };

        const ceContent = {
            headingMain: "Computer",
            headingAccent: "Engineering.",
            tagline: `We don't just build software — we <span class="love-word">design</span> systems, <span class="love-word">architect</span> algorithms, and <span class="love-word">empower</span> the future of technology through scalable computing.`,
            quote: `<p>"Computing is not about computers, it's about life and solving real-world challenges through logic."<cite>— GMIU CE Faculty, 2026</cite></p>`,
            terminalTitle: "ce_department.js",
            terminalBody: `
                <div class="code-line"><span class="cmt">// Department of Computer Engineering</span></div>
                <div class="code-line"><span class="kw">const</span> <span class="var">ceDept</span> = {</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">vision</span>: <span class="str">'Architecting scalable software'</span>,</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">degrees</span>: [<span class="str">'B.Tech CE'</span>, <span class="str">'Diploma CE'</span>],</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">headOfDept</span>: <span class="str">'Prof. Dhaval Chandarana'</span>,</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">inchargeHod</span>: <span class="str">'Prof. Ekta Unagar'</span>,</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">labs</span>: [<span class="str">'Supercomputing Lab'</span>, <span class="str">'Web Tech Hub'</span>],</div>
                <div class="code-line">&nbsp;&nbsp;<span class="key">coreSubjects</span>: [</div>
                <div class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;<span class="str">'Algorithms'</span>, <span class="str">'System Design'</span>, <span class="str">'Cloud Computing'</span></div>
                <div class="code-line">&nbsp;&nbsp;]</div>
                <div class="code-line">};</div>
                <div class="code-line"></div>
                <div class="code-line"><span class="var">Develop</span>.<span class="fn">By</span> = <span class="str">'<span class="blink-cursor">DK Dholakiya...|</span>'</span>;
            `,
            pill1: `<span class="pill-icon">🎓</span><span>B.Tech & Diploma CE</span>`,
            pill2: `<span class="pill-icon">💻</span><span>Supercomputing Lab</span>`,
            pill3: `<span class="pill-icon">🚀</span><span>System Architectures</span>`,
            pill4: `<span class="pill-icon">🧠</span><span>Logic & Problem Solving</span>`
        };

        function updateDepartmentContent(isCe) {
            document.body.classList.toggle('ce-active', isCe);
            
            const content = isCe ? ceContent : itContent;
            headingMain.innerText = content.headingMain;
            headingAccent.innerText = content.headingAccent;
            homeTagline.innerHTML = content.tagline;
            homeQuote.innerHTML = content.quote;
            terminalTitle.innerText = content.terminalTitle;
            terminalBody.innerHTML = content.terminalBody;
            pill1.innerHTML = content.pill1;
            pill2.innerHTML = content.pill2;
            pill3.innerHTML = content.pill3;
            pill4.innerHTML = content.pill4;
            
            if (isCe) {
                itBtn.classList.remove('active');
                ceBtn.classList.add('active');
                localStorage.setItem('portal_dept', 'CE');
                document.title = "CE Department — GMIU Faculty Portal";
            } else {
                ceBtn.classList.remove('active');
                itBtn.classList.add('active');
                localStorage.setItem('portal_dept', 'IT');
                document.title = "IT Department — GMIU Faculty Portal";
            }
        }

        if (itBtn && ceBtn) {
            itBtn.addEventListener('click', () => updateDepartmentContent(false));
            ceBtn.addEventListener('click', () => updateDepartmentContent(true));
        }

        // Always default to IT department on page load
        updateDepartmentContent(false);

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