<?php
// Common header fragment: theme toggle only; logo moved into sidebars/forms as needed
?>
<div class="site-header">
	<div class="theme-toggle" style="position:fixed; right:18px; top:18px; z-index:70;">
		<button id="themeToggle" aria-label="Toggle dark mode" title="Toggle dark mode" style="cursor:pointer;border:0;background:transparent;font-size:20px;padding:8px;border-radius:8px;">🌙</button>
	</div>
</div>

<script>
// Theme toggle: stores preference in localStorage and applies data-theme on documentElement
(function(){
	const btn = document.getElementById('themeToggle');
	if(!btn) return;

	const apply = (theme) => {
		if(theme === 'dark') {
			document.documentElement.setAttribute('data-theme','dark');
			btn.textContent = '☀️';
		} else {
			document.documentElement.removeAttribute('data-theme');
			btn.textContent = '🌙';
		}
	};

	// Determine initial theme: saved preference or system preference
	const saved = localStorage.getItem('innoventory-theme');
	if(saved) {
		apply(saved);
	} else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
		apply('dark');
	}

	btn.addEventListener('click', function(){
		const current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
		const next = current === 'dark' ? 'light' : 'dark';
		apply(next);
		localStorage.setItem('innoventory-theme', next);
	});
})();
</script>