document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    if (!startDateInput || !endDateInput) return;

    startDateInput.addEventListener('change', function () {
        if (this.value) {
            const startDate = new Date(this.value);
            startDate.setDate(startDate.getDate() + 30);
            const year = startDate.getFullYear();
            const month = String(startDate.getMonth() + 1).padStart(2, '0');
            const day = String(startDate.getDate()).padStart(2, '0');
            endDateInput.value = `${year}-${month}-${day}`;
            this.form.submit();
        }
    });

    endDateInput.addEventListener('change', function () {
        if (this.value) {
            this.form.submit();
        }
    });
});
