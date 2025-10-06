document.addEventListener('DOMContentLoaded', () => {
    const listRoot = document.getElementById('students-list');
    const pagerRoot = document.getElementById('students-pagination');
    const form = document.getElementById('student-form');
    const searchInput = document.getElementById('student-search');

    if (!listRoot || !pagerRoot) return;

    let state = { page: 1, perPage: 9, search: '' };

    function fetchStudents() {
        const params = new URLSearchParams({
            action: 'list',
            page: String(state.page),
            per_page: String(state.perPage),
        });
        if (state.search.trim()) params.set('search', state.search.trim());

        return fetch(`students.php?${params.toString()}`)
            .then(r => r.json())
            .then(json => {
                if (json.status !== 'success') throw new Error(json.message || 'Failed');
                return json;
            });
    }

    function renderList(rows) {
        listRoot.innerHTML = rows.map(s => `
            <div class="card">
                <div class="card-body">
                    <div class="card-title">${escapeHtml(s.name)}</div>
                    <div class="card-meta">${escapeHtml(s.department)} • Year ${Number(s.year)}</div>
                    <div class="card-text">${s.email ? escapeHtml(s.email) : ''}</div>
                    <div>
                        <button class="btn btn-danger" data-del="${s.id}">Delete</button>
                    </div>
                </div>
            </div>
        `).join('');

        listRoot.querySelectorAll('button[data-del]').forEach(btn => {
            btn.addEventListener('click', () => deleteStudent(btn.getAttribute('data-del')));
        });
    }

    function renderPager(p) {
        const { page, total_pages } = p;
        pagerRoot.innerHTML = '';
        const prev = document.createElement('button');
        prev.textContent = 'Prev';
        prev.disabled = page <= 1;
        prev.addEventListener('click', () => { state.page = page - 1; load(); });
        pagerRoot.appendChild(prev);
        for (let i = 1; i <= total_pages; i++) {
            const b = document.createElement('button');
            b.textContent = String(i);
            if (i === page) b.classList.add('active');
            b.addEventListener('click', () => { state.page = i; load(); });
            pagerRoot.appendChild(b);
        }
        const next = document.createElement('button');
        next.textContent = 'Next';
        next.disabled = page >= total_pages;
        next.addEventListener('click', () => { state.page = page + 1; load(); });
        pagerRoot.appendChild(next);
    }

    function load() {
        fetchStudents().then(({ data, pagination }) => {
            renderList(data);
            renderPager(pagination);
        }).catch(err => {
            console.error(err);
            listRoot.innerHTML = '<p>Failed to load students.</p>';
            pagerRoot.innerHTML = '';
        });
    }

    function createStudent(payload) {
        const body = new URLSearchParams(Object.entries(payload));
        return fetch('students.php?action=create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString(),
        }).then(r => r.json());
    }

    function deleteStudent(id) {
        const body = new URLSearchParams({ id: String(id) });
        fetch('students.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString(),
        }).then(r => r.json()).then(resp => {
            if (resp.status === 'success') load();
            else alert(resp.message || 'Delete failed');
        }).catch(() => alert('Delete failed'));
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', '\'': '&#39;' }[s]));
    }

    if (form) {
        form.addEventListener('submit', e => {
            e.preventDefault();
            const name = document.getElementById('student-name').value.trim();
            const dept = document.getElementById('student-dept').value.trim();
            const year = parseInt(document.getElementById('student-year').value, 10);
            const email = document.getElementById('student-email').value.trim();
            if (!name || !dept || !(year >= 1 && year <= 4)) { alert('Fill required fields'); return; }
            createStudent({ name, department: dept, year, email }).then(resp => {
                if (resp.status === 'success') {
                    form.reset();
                    state.page = 1;
                    load();
                } else {
                    alert(resp.message || 'Create failed');
                }
            }).catch(() => alert('Create failed'));
        });
    }

    if (searchInput) {
        let timer = null;
        searchInput.addEventListener('input', () => {
            state.search = searchInput.value;
            state.page = 1;
            clearTimeout(timer);
            timer = setTimeout(load, 250);
        });
    }

    load();
});


