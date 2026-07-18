<?php
$footer_class = isset($footer_class) ? $footer_class : 'rp-footer text-center';
?>
<!-- Footer -->
<footer class="<?php echo $footer_class; ?>">
    <div class="footer-divider"></div>
    <p class="footer-text">
        &copy; 2026 Department of CE & IT 
        <span class="footer-sep">&nbsp;·&nbsp;</span> 
        Designed with <span class="footer-heart">♥</span> by 
        <a href="https://github.com/dkdholakiya" target="_blank" class="designer-link">Dev Dholakiya</a>
    </p>
</footer>

<style>
.footer-divider {
    width: 100%;
    max-width: 1200px;
    height: 1px;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.08) 50%, rgba(255, 255, 255, 0) 100%);
    margin: 40px auto 20px;
}

.footer-text {
    margin: 0;
    font-size: 13.5px;
    color: rgba(255, 255, 255, 0.45);
    letter-spacing: 0.5px;
    transition: color 0.3s ease;
}

.footer-sep {
    color: rgba(255, 255, 255, 0.25);
}

.footer-heart {
    color: #ef4444;
    display: inline-block;
    animation: heartBeat 1.8s infinite;
    text-shadow: 0 0 6px rgba(239, 68, 68, 0.6);
}

.designer-link {
    color: #cbd5e1;
    text-decoration: none;
    font-weight: 700;
    position: relative;
    transition: color 0.3s ease, text-shadow 0.3s ease;
}

.designer-link::after {
    content: '';
    position: absolute;
    width: 100%;
    transform: scaleX(0);
    height: 2px;
    bottom: -2px;
    left: 0;
    background: linear-gradient(90deg, #ef4444 0%, #3b82f6 100%);
    transform-origin: bottom right;
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.designer-link:hover {
    color: #ffffff;
    text-shadow: 0 0 12px rgba(255, 255, 255, 0.45);
}

.designer-link:hover::after {
    transform: scaleX(1);
    transform-origin: bottom left;
}

@keyframes heartBeat {
    0% { transform: scale(1); }
    14% { transform: scale(1.15); }
    28% { transform: scale(1); }
    42% { transform: scale(1.15); }
    70% { transform: scale(1); }
}
</style>
