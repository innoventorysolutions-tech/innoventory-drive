<?php
session_start();
include "db.php";

// Auth Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$adminName = $_SESSION['admin_name'] ?? 'Admin';

// fetch pending and approved users
$pendingArr = [];
$approvedArr = [];

// Fetch Pending
$pendingRes = mysqli_query($conn, "SELECT * FROM users WHERE status='pending' ORDER BY id DESC");
if ($pendingRes) {
    while($row = mysqli_fetch_assoc($pendingRes)) {
        $pendingArr[] = $row;
    }
}

// Fetch Approved
$approvedRes = mysqli_query($conn, "SELECT * FROM users WHERE status='approved' ORDER BY id DESC");
if ($approvedRes) {
    while($row = mysqli_fetch_assoc($approvedRes)) {
        $approvedArr[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Innoventory Drive - Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f8f9fa; --panel:#fff; --muted:#666; --accent:#007bff;
      --sidebar:#111; --sidebarText:#fff; --surface-border:#e6e6e6;
    }
    body{font-family:'Poppins',sans-serif;margin:0;display:flex;min-height:100vh;background:var(--bg);color:var(--text);transition:background .25s,color .25s}
    body.dark{ --bg:#121212; --panel:#1e1e1e; --muted:#bdbdbd; --accent:#66aaff; --text:#eee; --sidebar:#000; --sidebarText:#fff; --surface-border:#333 }

    .sidebar{width:240px;background:var(--sidebar);color:var(--sidebarText);padding:24px;box-sizing:border-box;border-right:1px solid rgba(0,0,0,.06)}
    .logo{display:flex;gap:12px;align-items:center;margin-bottom:22px}
    .logo-image{width:44px;height:44px;border-radius:8px;background:linear-gradient(135deg,var(--accent),#0056d2);display:flex;align-items:center;justify-content:center;color:#fff}
    .logo-text{font-weight:700;line-height:1}
    .new-btn{width:100%;padding:10px;border-radius:8px;border:none;background:var(--accent);color:#fff;font-weight:600;cursor:pointer;margin-bottom:18px}
    .nav-menu ul{list-style:none;padding:0;margin:10px 0}
    .nav-menu li{margin-bottom:10px}
    .nav-menu a{display:block;color:rgba(255,255,255,.85);text-decoration:none;padding:8px;border-radius:8px}
    .nav-menu a.active{background:rgba(255,255,255,.06);}

    main.content{flex:1;padding:18px 28px;box-sizing:border-box;min-height:100vh;background:var(--bg)}
    .topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;gap:16px}
    .left-tools{display:flex;gap:12px;align-items:center}
    .search-box{display:flex;align-items:center;background:var(--panel);border:1px solid var(--surface-border);padding:8px 12px;border-radius:8px;min-width:280px}
    .search-box input{border:0;background:transparent;outline:none;width:100%;color:var(--text)}
    .theme-toggle{background:var(--accent);color:#fff;border:none;padding:8px 12px;border-radius:8px;cursor:pointer;font-weight:600}

    .profile{position:relative}
    .avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#0056d2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;cursor:pointer}
    .profile-menu{position:absolute;right:0;top:56px;background:var(--panel);color:var(--text);border:1px solid var(--surface-border);border-radius:10px;padding:8px;min-width:150px;box-shadow:0 10px 30px rgba(0,0,0,.08);display:none;z-index:100;}
    .profile-menu a{display:block;padding:8px;border-radius:6px;text-decoration:none;color:var(--text)}
    .profile-menu a:hover{background:rgba(0,0,0,.04)}

    .view-tabs{display:flex;gap:10px;margin-bottom:16px}
    .tab-btn{background:var(--panel);border:1px solid var(--surface-border);padding:8px 12px;border-radius:8px;cursor:pointer}
    .tab-btn.active{background:var(--accent);color:#fff;border-color:transparent}

    .grid-area{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px}
    .card{background:var(--panel);border:1px solid var(--surface-border);border-radius:10px;padding:14px;display:flex;flex-direction:column;gap:10px;min-height:110px}
    .icon{width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.04);font-weight:700}
    .name{font-weight:600;word-break:break-word}
    .meta{font-size:13px;color:var(--muted)}

    .panel{background:var(--panel);border:1px solid var(--surface-border);padding:16px;border-radius:12px;box-sizing:border-box}
    table{width:100%;border-collapse:collapse}
    th,td{padding:10px 12px;border-bottom:1px solid var(--surface-border);text-align:left;color:var(--text)}
    th{color:var(--accent);font-weight:600}
    .action-btn{padding:6px 10px;border-radius:8px;border:none;cursor:pointer;font-weight:600}
    .approve{background:var(--accent);color:#fff}
    .deny{background:#dc3545;color:#fff}
    .small{font-size:13px;color:var(--muted)}

    .section-title{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
    .flex-row{display:flex;gap:12px;align-items:center}
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);display:none;align-items:center;justify-content:center;z-index:999}
    .modal-overlay.active{display:flex}
    .modal{background:var(--panel);padding:18px;border-radius:12px;min-width:320px;border:1px solid var(--surface-border);color:var(--text)}
    .modal h3{margin-bottom:12px}
    .modal input, .modal select{width:100%;padding:10px;margin-bottom:10px;border-radius:8px;border:1px solid var(--surface-border);background:transparent;color:var(--text);box-sizing:border-box}
    .modal .row{display:flex;gap:8px}
    .modal .row input{flex:1}
    .modal .btns{display:flex;gap:8px;justify-content:flex-end;margin-top:6px}
    .btn-prim{background:var(--accent);color:#fff;border:none;padding:8px 12px;border-radius:8px;cursor:pointer}
    .btn-sec{background:transparent;border:1px solid var(--surface-border);padding:8px 12px;border-radius:8px;cursor:pointer}

    @media (max-width:900px){ .sidebar{display:none} .grid-area{grid-template-columns:repeat(auto-fill,minmax(140px,1fr))} }
  </style>
</head>
<body>
  <aside class="sidebar" aria-label="sidebar">
    <div class="logo">
      <div class="logo-image">I</div>
      <div class="logo-text">INNOVENTORY<br><span style="font-weight:500;font-size:13px">SOLUTIONS</span></div>
    </div>
    <button class="new-btn" id="globalAddBtn">+ New</button>
    <nav class="nav-menu">
      <ul>
        <li><a href="#" class="active" id="navHome">Home</a></li>
        <li><a href="#" id="navDrive">My Drive</a></li>
        <li><a href="#">Shared</a></li>
        <li><a href="#">Computers</a></li>
        <li style="margin-top:12px"><a href="#" id="navUsers">Users</a></li>
        <li><a href="#">Storage</a></li>
      </ul>
    </nav>
    <div style="margin-top:18px;font-size:13px;color:rgba(255,255,255,.7)">Signed in as</div>
    <div style="display:flex;gap:10px;align-items:center;margin-top:10px">
      <div class="avatar" id="sidebarAvatar"><?= strtoupper(substr($adminName, 0, 1)) ?></div>
      <div style="font-weight:600"><?= htmlspecialchars($adminName) ?></div>
    </div>
  </aside>

  <main class="content">
    <div class="topbar">
      <div class="left-tools">
        <div class="search-box" title="Search">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="opacity:.7;margin-right:8px">
            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2"/>
          </svg>
          <input id="globalSearch" placeholder="Search..." />
        </div>

        <div class="view-tabs" role="tablist" aria-label="views">
          <button class="tab-btn active" data-view="grid" id="tabGrid">Grid</button>
          <button class="tab-btn" data-view="admin" id="tabAdmin">Admin</button>
        </div>
      </div>

      <div class="top-actions">
        <button class="theme-toggle" id="themeToggle">🌙 Dark Mode</button>

        <div class="profile" id="profileWrap">
          <div class="avatar" id="profileAvatar"><?= strtoupper(substr($adminName, 0, 1)) ?></div>
          <div class="profile-menu" id="profileMenu" aria-hidden="true">
            <a href="#" id="profileName"><?= htmlspecialchars($adminName) ?></a>
            <a href="#" id="settingsBtn">Settings</a>
            <a href="logout.php" id="logoutBtn" style="color:#dc3545">Logout</a>
          </div>
        </div>
      </div>
    </div>

    <!-- GRID VIEW -->
    <section id="gridView">
      <div class="section-title">
        <h2 style="margin:0">My Drive</h2>
        <div class="flex-row">
          <button class="icon-btn" id="addFolderBtn">+ Folder</button>
          <button class="icon-btn" id="addFileBtn">+ File</button>
        </div>
      </div>

      <div class="grid-area panel" id="itemsGrid" style="padding:16px;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));"></div>
    </section>

    <!-- ADMIN VIEW -->
    <section id="adminView" style="display:none">
      <div class="section-title">
        <h2 style="margin:0">Pending Requests</h2>
        <div class="flex-row">
          <button class="btn-prim" id="openAddUser">Add User</button>
          <button class="btn-sec" id="bulkApproveBtn">Bulk Approve</button>
          <button class="btn-sec" id="bulkDenyBtn">Bulk Deny</button>
          <input type="file" id="importCsvInput" accept=".csv" style="display:none;">
          <button class="btn-sec" id="importCsvBtn">Import CSV</button>
          <button class="btn-prim" id="exportApprovedBtn">Export CSV</button>
        </div>
      </div>

      <div class="panel" style="margin-bottom:18px">
        <table id="pendingTable">
          <thead>
            <tr>
              <th><input type="checkbox" id="selectAllPending"></th>
              <th>Name</th><th>Email</th><th>Reason</th><th>Location</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pendingArr as $u): ?>
            <tr>
              <td><input type="checkbox" class="pending-check" data-id="<?= $u['id'] ?>"></td>
              <td><?= htmlspecialchars($u['name']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars($u['reason']) ?></td>
              <td><?= htmlspecialchars($u['location']) ?></td>
              <td>
                <button class="action-btn approve" data-id="<?= $u['id'] ?>">Approve</button>
                <button class="action-btn deny" data-id="<?= $u['id'] ?>">Deny</button>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($pendingArr)): ?>
            <tr><td colspan="6" style="text-align:center;color:#999">No pending requests</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="section-title">
        <h3 style="margin:0">Approved Users</h3>
      </div>

      <div class="panel">
        <table id="approvedTable">
          <thead>
            <tr><th>Name</th><th>Email</th><th>Reason</th><th>Location</th></tr>
          </thead>
          <tbody>
             <?php foreach ($approvedArr as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['name']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars($u['reason']) ?></td>
              <td><?= htmlspecialchars($u['location']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <div class="modal-overlay" id="modalOverlay">
    <div class="modal" id="modalContent"></div>
  </div>

  <script>
    const qs = s => document.querySelector(s);
    const storage = {
      get(key, fallback){ try{ const v=localStorage.getItem(key); return v?JSON.parse(v):fallback }catch(e){return fallback} },
      set(key,val){localStorage.setItem(key,JSON.stringify(val))}
    };

    let items = storage.get('drive_items', [
      {id:uid(),type:'folder',name:'Project Docs',meta:'3 items'},
      {id:uid(),type:'file',name:'Presentation.pdf',meta:'120 KB'}
    ]);
    
    // UI References
    const gridView = qs('#gridView'), adminView = qs('#adminView');
    const itemsGrid = qs('#itemsGrid');
    const modalOverlay = qs('#modalOverlay'), modalContent = qs('#modalContent');
    const tabGrid = qs('#tabGrid'), tabAdmin = qs('#tabAdmin');
    const themeToggle = qs('#themeToggle');

    // Theme Logic
    const currentTheme = storage.get('theme','light');
    if(currentTheme === 'dark'){ document.body.classList.add('dark'); themeToggle.textContent='☀️ Light Mode' }
    themeToggle.addEventListener('click', () => {
      document.body.classList.toggle('dark');
      const isDark = document.body.classList.contains('dark');
      themeToggle.textContent = isDark ? '☀️ Light Mode' : '🌙 Dark Mode';
      storage.set('theme', isDark ? 'dark' : 'light');
    });

    // Profile Menu
    qs('#profileAvatar').addEventListener('click', (e)=> {
      e.stopPropagation();
      const pm = qs('#profileMenu'); pm.style.display = pm.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', e=> { if(!qs('#profileWrap').contains(e.target)) qs('#profileMenu').style.display='none'; });

    // Tab Switching
    function setView(v){ 
      if(v==='grid'){ 
        gridView.style.display='block'; adminView.style.display='none'; 
        tabGrid.classList.add('active'); tabAdmin.classList.remove('active'); 
      } else { 
        gridView.style.display='none'; adminView.style.display='block'; 
        tabAdmin.classList.add('active'); tabGrid.classList.remove('active'); 
      } 
    }
    tabGrid.addEventListener('click', ()=> setView('grid'));
    tabAdmin.addEventListener('click', ()=> setView('admin'));

    // Nav Link handlers
    qs('#navHome').addEventListener('click', ()=> setView('grid'));
    qs('#navDrive').addEventListener('click', ()=> setView('grid'));
    qs('#navUsers').addEventListener('click', ()=> setView('admin'));


    function uid(){ return Date.now().toString(36)+Math.random().toString(36).slice(2,8); }

    // Render Drive Items (Client-side mainly for demo/storage)
    function renderItems(){
      itemsGrid.innerHTML=''; items.forEach(it=>{
        const card = document.createElement('div'); card.className='card';
        card.innerHTML = `<div class="icon">${it.type==='folder'?'📁':'📄'}</div>
                          <div class="name">${escapeHtml(it.name)}</div>
                          <div class="meta small">${escapeHtml(it.meta||'')}</div>
                          <div style="margin-top:auto;display:flex;justify-content:space-between">
                            <button class="btn-sec" data-id="${it.id}" data-act="open">Open</button>
                            <button class="btn-sec" data-id="${it.id}" data-act="delete">Delete</button>
                          </div>`;
        itemsGrid.appendChild(card);
      });
    }

    // Modal Logic
    function openModalChoice(){
      modalContent.innerHTML = `<h3>Create</h3>
        <div style="display:flex;gap:8px;margin-top:8px">
          <button class="btn-prim" id="mNewFolder">New Folder</button>
          <button class="btn-prim" id="mNewFile">New File</button>
          <button class="btn-sec" id="mNewUser">New User</button>
          <button class="btn-sec" id="mClose">Close</button>
        </div>`;
      qs('#mNewFolder').onclick = ()=> openFolderModal();
      qs('#mNewFile').onclick = ()=> openFileModal();
      qs('#mNewUser').onclick = ()=> openUserModal();
      qs('#mClose').onclick = closeModal;
      modalOverlay.classList.add('active');
    }
    function closeModal(){ modalOverlay.classList.remove('active'); modalContent.innerHTML=''; }

    function openFolderModal(pref=''){ 
      modalContent.innerHTML = `<h3>Create Folder</h3><input id="folderName" placeholder="Folder name" value="${escapeHtml(pref)}"><div class="btns"><button class="btn-sec" id="fCancel">Cancel</button><button class="btn-prim" id="fCreate">Create</button></div>`;
      qs('#fCancel').onclick = closeModal;
      qs('#fCreate').onclick = ()=> {
        const name = qs('#folderName').value.trim(); if(!name) return alert('Enter folder name');
        items.unshift({id:uid(),type:'folder',name,meta:'0 items'}); storage.set('drive_items', items); renderItems(); closeModal();
      };
      modalOverlay.classList.add('active');
    }

    function openFileModal(){
      modalContent.innerHTML = `<h3>Upload / Add File</h3><input id="fileName" placeholder="File name (e.g., report.pdf)"><input id="fileSize" placeholder="Size (e.g., 120 KB)"><div class="btns"><button class="btn-sec" id="fiCancel">Cancel</button><button class="btn-prim" id="fiCreate">Add File</button></div>`;
      qs('#fiCancel').onclick = closeModal;
      qs('#fiCreate').onclick = ()=> {
        const name = qs('#fileName').value.trim(); const size = qs('#fileSize').value.trim();
        if(!name) return alert('Enter file name');
        items.unshift({id:uid(),type:'file',name,meta:size||''}); storage.set('drive_items', items); renderItems(); closeModal();
      };
      modalOverlay.classList.add('active');
    }

    function openUserModal(){
      // Redirect to apply for now, or implement an AJAX add
      window.location.href = "apply.php";
    }

    qs('#globalAddBtn').addEventListener('click', ()=> openModalChoice());
    qs('#openAddUser').addEventListener('click', ()=> openUserModal());
    qs('#addFolderBtn').addEventListener('click', ()=> openFolderModal());
    qs('#addFileBtn').addEventListener('click', ()=> openFileModal());
    modalOverlay.addEventListener('click', e=> { if(e.target===modalOverlay) closeModal(); });


    // --- PHP / SERVER INTERACTION ---

    // Approve/Deny Single
    document.querySelector('#pendingTable tbody').addEventListener('click', e=>{
      const btn = e.target.closest('button'); if(!btn) return;
      const id = btn.dataset.id;
      if(btn.classList.contains('approve')) updateStatus([id], 'approve');
      else if(btn.classList.contains('deny')) updateStatus([id], 'deny');
    });

    // Bulk Actions
    qs('#bulkApproveBtn').addEventListener('click', ()=>{
      const ids = Array.from(document.querySelectorAll('.pending-check:checked')).map(cb=>cb.dataset.id);
      if(ids.length>0) updateStatus(ids,'approve'); else alert('Select at least one');
    });
    qs('#bulkDenyBtn').addEventListener('click', ()=>{
      const ids = Array.from(document.querySelectorAll('.pending-check:checked')).map(cb=>cb.dataset.id);
      if(ids.length>0) updateStatus(ids,'deny'); else alert('Select at least one');
    });

    // Select All
    const selectAll = qs('#selectAllPending');
    if(selectAll) {
        selectAll.addEventListener('change', e=>{
            const checked = e.target.checked;
            document.querySelectorAll('.pending-check').forEach(cb=>cb.checked=checked);
        });
    }

    function updateStatus(ids, action){
      fetch('actions.php',{ 
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'action='+action+'&ids[]='+ids.join('&ids[]=')
      }).then(r=>r.json()).then(r=>{
        if(r.success) location.reload();
        else alert('Action failed: ' + (r.message || 'Unknown error'));
      }).catch(e=>alert('Network error'));
    }

    // Export CSV
    qs('#exportApprovedBtn').addEventListener('click', ()=>{
      const rows = [];
      const headers = ['Name','Email','Reason','Location'];
      rows.push(headers.join(','));
      
      document.querySelectorAll('#approvedTable tbody tr').forEach(tr => {
          const cells = tr.querySelectorAll('td');
          if(cells.length >= 4) {
              const rowData = [
                  cells[0].textContent,
                  cells[1].textContent,
                  cells[2].textContent,
                  cells[3].textContent
              ];
              rows.push(rowData.map(c => `"${c.replace(/