function toggleImportDetail(importId, rowElement) {
    const detailRow = document.getElementById(`detail-row-${importId}`);
    const allDetailRows = document.querySelectorAll('.detail-row');
    const allImportRows = document.querySelectorAll('.import-row');
    
    if (!detailRow) {
        return;
    }

    // Close all other detail rows
    allDetailRows.forEach(row => {
        if (row.id !== `detail-row-${importId}`) {
            row.style.display = 'none';
        }
    });
    
    // Remove selected class from all rows
    allImportRows.forEach(row => {
        row.classList.remove('selected-row');
    });
    
    // Toggle current detail row
    if (detailRow.style.display === 'none' || detailRow.style.display === '') {
        detailRow.style.display = 'table-row';
        rowElement.classList.add('selected-row');
    } else {
        detailRow.style.display = 'none';
        rowElement.classList.remove('selected-row');
    }
}


function updateImportCount(visibleCount) {
    const totalImports = document.querySelectorAll('.import-row').length;
    const summaryElement = document.querySelector('.summary-section small');
    if (summaryElement) {
        summaryElement.innerHTML = `Tổng cộng: <strong>${totalImports}</strong> phiếu nhập | Hiển thị: <strong>${visibleCount}</strong>`;
    }
}