<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Internship Companies</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .company {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            max-width: 600px;
        }
        .company img {
            max-width: 120px;
            max-height: 60px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .company h2 {
            margin: 5px 0;
            font-size: 1.4em;
        }
        .company p {
            margin: 5px 0;
            color: #555;
        }
        .company a {
            color: #1a0dab;
            text-decoration: none;
        }
        .company a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Available Internship Companies</h1>
    <div id="company-list">
        Loading companies...
    </div>

    <script>
        async function loadCompanies() {
            const container = document.getElementById('company-list');
            container.textContent = 'Loading companies...';
            try {
                const res = await fetch('/backend.php?action=getCompanies');
                const companies = await res.json();

                if (companies.error) {
                    container.textContent = 'Error: ' + companies.error;
                    return;
                }

                if (companies.length === 0) {
                    container.textContent = 'No companies found.';
                    return;
                }

                container.innerHTML = '';

                companies.forEach(c => {
                    const div = document.createElement('div');
                    div.className = 'company';

                    let logoHtml = c.clogo ? `<img src="${c.clogo}" alt="${c.cname} Logo">` : '';

                    div.innerHTML = `
                        ${logoHtml}
                        <h2>${c.cname}</h2>
                        <p><strong>Description:</strong> ${c.cdesc || 'N/A'}</p>
                        <p><strong>Department:</strong> ${c.dept || 'N/A'}</p>
                        <p><strong>Location:</strong> ${c.loc || 'N/A'}</p>
                        <p><strong>Size:</strong> ${c.size || 'N/A'}</p>
                        <p><strong>Website:</strong> ${c.website ? `<a href="${c.website}" target="_blank">${c.website}</a>` : 'N/A'}</p>
                        <p><strong>Contact Email:</strong> ${c.cmail || 'N/A'}</p>
                        <p><strong>Phone:</strong> ${c.ph || 'N/A'}</p>
                    `;
                    container.appendChild(div);
                });

            } catch (error) {
                container.textContent = 'Failed to load companies.';
                console.error(error);
            }
        }

        loadCompanies();
    </script>
</body>
</html>
