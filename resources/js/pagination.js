document.addEventListener('change', (event) => {
    const select = event.target;

    if (!(select instanceof HTMLSelectElement)
        || !select.matches('.r-pagination select[name="per_page"]')) {
        return;
    }

    const previousSize = Number(select.dataset.perPage);
    const currentPage = Number(select.dataset.currentPage);
    const newSize = Number(select.value);

    if (!Number.isSafeInteger(previousSize) || previousSize < 1
        || !Number.isSafeInteger(currentPage) || currentPage < 1
        || !Number.isSafeInteger(newSize) || newSize < 1
        || previousSize === newSize) {
        return;
    }

    const firstItem = (currentPage - 1) * previousSize + 1;
    const newPage = Math.ceil(firstItem / newSize);
    const url = new URL(window.location.href);

    url.searchParams.set('per_page', String(newSize));
    url.searchParams.set(select.dataset.pageName || 'page', String(newPage));

    window.location.assign(url.toString());
});
