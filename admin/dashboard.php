<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        input, textarea { margin: 5px 0; width: 300px; }
        button { margin: 5px; }
        table { border-collapse: collapse; width: 100%; max-width: 900px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1>Welcome, Admin</h1>
    <button id="logout-btn">Logout</button>

    <h2>Add New Company</h2>
    <form id="add-company-form">
        <input type="text" id="cname" placeholder="Company Name" required><br>
        <input type="text" id="clogo" placeholder="Logo URL"><br>
        <textarea id="cdesc" placeholder="Description"></textarea><br>
        <input type="text" id="dept" placeholder="Department"><br>
        <input type="text" id="loc" placeholder="Location"><br>
        <input type="text" id="size" placeholder="Company Size"><br>
        <input type="text" id="website" placeholder="Website URL"><br>
        <input type="email" id="cmail" placeholder="Contact Email"><br>
        <input type="text" id="ph" placeholder="Phone"><br>
        <button type="submit">Add Company</button>
    </form>

    <h2>Company List</h2>
    <table id="company-table">
        <thead>
            <tr>
                <th>ID</th><th>Logo</th><th>Name</th><th>Description</th><th>Dept</th><th>Location</th><th>Size</th><th>Website</th><th>Email</th><th>Phone</th><th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        async function checkLogin() {
            // Try to fetch companies; if unauthorized, redirect to login
            const res = await fetch('backend.php?action=getCompanies');
            if (res.status === 401) {
                window.location.href = 'login.php';
                return false;
            }
            return true;
        }

        async function loadCompanies() {
            const res = await fetch('backend.php?action=getCompanies');
            const companies = await res.json();
            const tbody = document.querySelector('#company-table tbody');
            tbody.innerHTML = '';

            companies.forEach(c => {
                const tr = document.createElement('tr');

                tr.innerHTML = `
                    <td>${c.cid}</td>
                    <td>${c.clogo ? `<img src="${c.clogo}" alt="Logo" width="50">` : ''}</td>
                    <td contenteditable="true" data-field="cname" data-id="${c.cid}">${c.cname}</td>
                    <td contenteditable="true" data-field="cdesc" data-id="${c.cid}">${c.cdesc}</td>
                    <td contenteditable="true" data-field="dept" data-id="${c.cid}">${c.dept}</td>
                    <td contenteditable="true" data-field="loc" data-id="${c.cid}">${c.loc}</td>
                    <td contenteditable="true" data-field="size" data-id="${c.cid}">${c.size}</td>
                    <td contenteditable="true" data-field="website" data-id="${c.cid}">${c.website}</td>
                    <td contenteditable="true" data-field="cmail" data-id="${c.cid}">${c.cmail}</td>
                    <td contenteditable="true" data-field="ph" data-id="${c.cid}">${c.ph}</td>
                    <td>
                        <button onclick="saveCompany(${c.cid})">Save</button>
                        <button onclick="deleteCompany(${c.cid})">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        async function saveCompany(cid) {
            const fields = ['cname','cdesc','dept','loc','size','website','cmail','ph'];
            let updated = {};
            fields.forEach(field => {
                const td = document.querySelector(`td[data-field="${field}"][data-id="${cid}"]`);
                if(td) updated[field] = td.textContent.trim();
            });

            const res = await fetch(`backend.php?action=updateCompany&cid=${cid}`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(updated)
            });
            const data = await res.json();
            alert(data.success ? 'Company updated!' : 'Update failed');
            loadCompanies();
        }

        async function deleteCompany(cid) {
            if (!confirm('Are you sure to delete this company?')) return;
            const res = await fetch(`backend.php?action=deleteCompany&cid=${cid}`, {
                method: 'POST'
            });
            const data = await res.json();
            alert(data.success ? 'Deleted!' : 'Delete failed');
            loadCompanies();
        }

        document.getElementById('add-company-form').addEventListener('submit', async e => {
            e.preventDefault();
            const newCompany = {
                cname: document.getElementById('cname').value.trim(),
                clogo: document.getElementById('clogo').value.trim(),
                cdesc: document.getElementById('cdesc').value.trim(),
                dept: document.getElementById('dept').value.trim(),
                loc: document.getElementById('loc').value.trim(),
                size: document.getElementById('size').value.trim(),
                website: document.getElementById('website').value.trim(),
                cmail: document.getElementById('cmail').value.trim(),
                ph: document.getElementById('ph').value.trim(),
            };

            const res = await fetch('backend.php?action=addCompany', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(newCompany)
            });
            const data = await res.json();
            if (data.success) {
                alert('Company added!');
                e.target.reset();
                loadCompanies();
            } else {
                alert('Failed to add company: ' + (data.error || 'Unknown error'));
            }
        });

        document.getElementById('logout-btn').addEventListener('click', async () => {
            await fetch('backend.php?action=logout');
            window.location.href = 'login.php';
        });

        (async () => {
            if(await checkLogin()) {
                loadCompanies();
            }
        })();
    </script>
</body>
</html>
