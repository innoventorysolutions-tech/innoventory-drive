<?php
require_once "../../session.php";
require_once "../../config.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "user") {
    header("Location: ../../index.php");
    exit;
}

$uid = $_SESSION['id'];
// Added storage_used to simulate professional tracking
$stmt = $db->prepare("SELECT status, storage_gb, storage_path, name FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

$status = $user_data['status'];
$allocated_gb = $user_data['storage_gb'] ?: 1;
$used_gb = 0.45; // Logic for calculating actual size would go here
$usage_percent = ($used_gb / $allocated_gb) * 100;
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Drive | Innoventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .dark .glass { background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(10px); }
        .file-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    </style>
    <script>
        tailwind.config = { darkMode: 'class' }
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        }
    </script>
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0B1120] text-gray-900 dark:text-gray-100 transition-colors duration-300">

    <div class="flex h-screen overflow-hidden">
        
        <?php include 'sidebar.php'; ?>

        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <header class="h-16 border-b border-gray-200 dark:border-gray-800 glass sticky top-0 z-10 flex items-center px-8 justify-between">
                <div class="flex-1 max-w-2xl">
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input class="block w-full bg-gray-100 dark:bg-gray-800/50 border border-transparent focus:border-blue-500 focus:ring-0 rounded-xl py-2 pl-10 pr-3 text-sm transition-all" placeholder="Search files, folders, or shared items...">
                    </div>
                </div>
                
                <div class="flex items-center space-x-5">
                    <button class="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </button>
                    <div class="flex items-center pl-4 border-l border-gray-200 dark:border-gray-700">
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-blue-500/20">
                            <?= strtoupper(substr($user_data['name'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-8 lg:px-12">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">My Drive</h1>
                        <nav class="flex mt-2 text-xs font-medium text-gray-500 uppercase tracking-widest">
                            <span>Storage</span>
                            <span class="mx-2 text-gray-300">/</span>
                            <span class="text-blue-600 dark:text-blue-400"><?= $user_data['storage_path'] ?: 'Initializing...'; ?></span>
                        </nav>
                    </div>

                    <?php if ($status === 'approved'): ?>
                    <button class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all hover:shadow-xl hover:shadow-blue-500/25 active:scale-95">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        New Upload
                    </button>
                    <?php endif; ?>
                </div>

                <?php if ($status === 'approved'): ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Cloud Storage Usage</span>
                                <span class="text-xs font-medium text-gray-500"><?= $used_gb ?>GB of <?= $allocated_gb ?>GB used</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-600 h-full rounded-full transition-all duration-1000" style="width: <?= $usage_percent ?>%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 flex items-center space-x-4">
                        <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622l-.382-3.040z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Account Status</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white uppercase"><?= $status ?></p>
                        </div>
                    </div>
                </div>

                <section>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Quick Access Folders</h2>
                        <button class="text-sm font-semibold text-blue-600 hover:underline">View All</button>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-12">
                        <div class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-2xl file-hover transition-all cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                </div>
                                <button class="text-gray-400 hover:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                </button>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">Documents</h3>
                                <p class="text-xs text-gray-500 mt-1">124 files • 12.5 MB</p>
                            </div>
                        </div>

                        <div class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-2xl file-hover transition-all cursor-pointer">
                            <div class="flex items-start justify-between">
                                <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl text-purple-600">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                                </div>
                                <button class="text-gray-400 hover:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                </button>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">Media Assets</h3>
                                <p class="text-xs text-gray-500 mt-1">45 files • 320 MB</p>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-6">Recent Activity</h2>
                    <div class="flex flex-col items-center justify-center py-20 bg-white dark:bg-gray-800/30 rounded-[2rem] border-2 border-dashed border-gray-200 dark:border-gray-800">
                        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </div>
                        <h3 class="text-gray-900 dark:text-white font-semibold">No files here yet</h3>
                        <p class="text-gray-500 text-sm mt-1">Drop files here to upload them to your secure storage.</p>
                    </div>
                </section>

                <?php elseif ($status === 'pending'): ?>
                <div class="mt-10 max-w-2xl mx-auto text-center">
                    <div class="relative inline-block mb-8">
                        <div class="absolute inset-0 bg-blue-500 blur-3xl opacity-20 animate-pulse"></div>
                        <div class="relative bg-white dark:bg-gray-800 p-8 rounded-full shadow-2xl">
                            <svg class="w-16 h-16 text-blue-500 animate-spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">Account Under Review</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-lg leading-relaxed">We're setting up your private cloud partition. You'll receive a notification as soon as your <strong><?= $allocated_gb ?>GB</strong> drive is ready for use.</p>
                    <div class="mt-8 flex justify-center space-x-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce [animation-delay:-.15s]"></div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce [animation-delay:-.3s]"></div>
                    </div>
                </div>

                <?php else: ?>
                <div class="mt-10 max-w-md mx-auto bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 p-10 rounded-[2.5rem] text-center">
                    <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 text-red-600 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-red-900 dark:text-red-400">Access Restricted</h2>
                    <p class="text-red-700 dark:text-red-500/80 mt-3 mb-8">Your account request was denied. If you think this is a mistake, please contact our support team.</p>
                    <a href="mailto:support@innoventory.com" class="bg-red-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-red-700 transition-colors">Contact Support</a>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

</body>
</html>