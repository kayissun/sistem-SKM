<script>
    function salinTabelKeClipboard(id, btn) {
        const table = document.getElementById(id);
        if (!table) return;

        const html = table.outerHTML;
        const text = table.innerText;
        const labelAsli = btn.innerText;

        const tandaiSelesai = () => {
            btn.innerText = 'Tersalin!';
            setTimeout(() => { btn.innerText = labelAsli; }, 1800);
        };

        const tandaiGagal = () => {
            alert('Gagal menyalin otomatis. Coba pakai browser Chrome/Edge versi terbaru, atau blok tabel manual lalu Ctrl+C.');
        };

        if (navigator.clipboard && window.ClipboardItem) {
            const item = new ClipboardItem({
                'text/html': new Blob([html], { type: 'text/html' }),
                'text/plain': new Blob([text], { type: 'text/plain' }),
            });
            navigator.clipboard.write([item]).then(tandaiSelesai).catch(tandaiGagal);
        } else {
            const range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            try {
                document.execCommand('copy');
                tandaiSelesai();
            } catch (e) {
                tandaiGagal();
            }
            window.getSelection().removeAllRanges();
        }
    }
</script>
