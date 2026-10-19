const sidebar = document.querySelector('[data-sidebar]');
const openButton = document.querySelector('[data-sidebar-open]');
const closeButton = document.querySelector('[data-sidebar-close]');
const overlay = document.querySelector('[data-sidebar-overlay]');

const openSidebar = () => {
    document.body.classList.add('sidebar-open');
};

const closeSidebar = () => {
    document.body.classList.remove('sidebar-open');
};

openButton?.addEventListener('click', openSidebar);
closeButton?.addEventListener('click', closeSidebar);
overlay?.addEventListener('click', closeSidebar);

document.querySelectorAll('.sidebar-link').forEach((link) => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 900) {
            closeSidebar();
        }
    });
});
