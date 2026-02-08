import * as XLSX from 'xlsx';

document.addEventListener('DOMContentLoaded', () => {

    const exportPretestBtn = document.getElementById('exportExcelBtnPretest');

    if (exportPretestBtn) {
        exportPretestBtn.addEventListener('click', (e) => {
            e.preventDefault(); // prevent form submit

            const table = document.getElementById('pretestTable');
            if (!table) return;

            const rows = [];

            // ===== Title Row =====
            rows.push(['Pretest Grades']);

            // ===== Header Row =====
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.innerText.trim());
            });
            rows.push(headers);

            // ===== Body Rows =====
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach(td => {
                    row.push(td.innerText.trim());
                });

                // Avoid pushing "No pretest results yet."
                if (row.length > 1) {
                    rows.push(row);
                }
            });

            // ===== Create Excel =====
            const ws = XLSX.utils.aoa_to_sheet(rows);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Pretest Grades');

            XLSX.writeFile(wb, 'pretest-grades.xlsx');
        });
    }

});
