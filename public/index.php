<?php session_start();

require __DIR__.'/../bootstrap.php';

$items = $_SESSION['items'] ?? [];

if (!is_array($items)) {
    $response = new \Symfony\Component\HttpFoundation\Response('Whoops! Something went wrong', 500);

    $response->send();

    exit(1);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Luck Draw</title>
</head>
<body class="bg-gray-300">
    <div id="app" x-data="{
        settingsVisible: false,
        intervalId: null,
        itemsStr: 'Something went wrong',
        items: [
            <?php foreach ($items as $item): ?>
                '<?php echo $item; ?>',
            <?php endforeach; ?>
        ],
        init() {
            this.itemsStr = this.items.join('\n');
        },
        toggleSettings() {
            this.settingsVisible = !this.settingsVisible;
        },
        saveSettings() {
            this.items = this.itemsStr.split('\n');
        },
        test() {
            let marginTop = 0;
            const container = document.getElementById('name-container');
            let milliseconds = 0;

            this.intervalId = setInterval(() => {
                marginTop = marginTop - 2;

                if (marginTop <= 85) {
                    marginTop = 0;
                }

                let first = this.items.shift();

                this.items.push(first);

                container.style.marginTop = marginTop + 'px';

                milliseconds += 100;

                if (milliseconds >= 5000) {
                    var winner = this.items[Math.floor(Math.random()*this.items.length)];

                    this.items = [winner, ...this.items];


                    clearInterval(this.intervalId);
                }
            }, 100);
        }
    }">
        <button x-on:click="toggleSettings" class="fixed top-4 right-4" title="Settings">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
        </button>

        <div x-show="settingsVisible" class="fixed top-0 right-0 h-screen bg-white w-96">
            <div class="p-4 w-full">
                <label>
                    <textarea x-model="itemsStr" class="w-full border border-gray-400 p-2" rows="25"></textarea>
                </label>

                <div class="text-center mt-4">
                    <button x-on:click="saveSettings" class="mx-auto uppercase bg-gray-800 text-white px-4 py-2 rounded font-bold hover:bg-gray-700">Save</button>
                </div>
            </div>

            <button x-on:click="toggleSettings" class="fixed bottom-4 right-4" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </button>
        </div>

        <div class="max-w-xl m-auto mt-64 bg-white rounded pt-12 pb-16 overflow-hidden">
            <ul id="name-container" class="w-full max-h-2">
                <template x-for="item in items">
                    <li class="text-3xl text-center font-bold mb-8">
                        <span x-text="item"></span>
                    </li>
                </template>
            </ul>
        </div>

        <div class="text-center mt-12">
            <button @click="test" class="mx-auto uppercase bg-gray-700 text-white px-4 py-2 rounded font-medium">Start</button>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
