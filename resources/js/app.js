
const root = document.documentElement;

if (localStorage.getItem('admin-theme') === 'dark') {
    root.classList.add('dark');
}

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    document.getElementById('openSidebar')?.addEventListener('click', () => {
        sidebar?.classList.remove('translate-x-full');
        overlay?.classList.remove('hidden');
    });

    const closeSidebar = () => {
        sidebar?.classList.add('translate-x-full');
        overlay?.classList.add('hidden');
    };

    document.getElementById('closeSidebar')?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    document.getElementById('themeToggle')?.addEventListener('click', () => {
        root.classList.toggle('dark');
        localStorage.setItem(
            'admin-theme',
            root.classList.contains('dark') ? 'dark' : 'light'
        );
    });

    const search = document.getElementById('userSearch');
    const filter = document.getElementById('statusFilter');

    const filterUsers = () => {
        const query = search?.value.toLowerCase() || '';
        const status = filter?.value || '';

        document.querySelectorAll('.user-row').forEach(row => {
            const matchSearch = row.dataset.search.toLowerCase().includes(query);
            const matchStatus = !status || row.dataset.status === status;

            row.classList.toggle('hidden', !(matchSearch && matchStatus));
        });
    };

    search?.addEventListener('input', filterUsers);
    filter?.addEventListener('change', filterUsers);
});
