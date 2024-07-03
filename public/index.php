<?php session_start();

require __DIR__.'/../bootstrap.php';

$items = $_SESSION['items'] ?? [];

if (empty($items)) {
    $faker = Faker\Factory::create();

    $items = [];
    foreach (range(1, 5) as $i) {
        $name = str_replace("'", " ", $faker->unique()->name());
        $items[] = $name . ' - ' . $i;
    }
}

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

    <style>
        .confetti {
            position: absolute;
            top: 0;
            font-size: 1.6rem;
            animation: confetti-fall linear 5s infinite;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(-100%) rotate(0);
            }
            100% {
                transform: translateY(100vh) rotate(180deg);
            }
        }
    </style>
</head>
<body class="bg-gray-300">
    <div id="app" x-data="{
        settingsVisible: false,
        intervalId: null,
        congratulationsInternalId: null,
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
        congratulations() {
            let milliseconds = 100;

            const showConfetti = () => {
                const confettiContainer = document.querySelector('#confetti-container');

                const confetti = document.createElement('div');
                confetti.textContent = '🎉';
                confetti.classList.add('confetti');
                confetti.style.left = Math.random() * innerWidth + 'px';
                confettiContainer.appendChild(confetti);

                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            };

            this.congratulationsInternalId = setInterval(() => {
                showConfetti();

                milliseconds += 100;

                if (milliseconds >= 5000) {
                    clearInterval(this.congratulationsInternalId);
                }
            }, 100);
        },
        start() {
            const wheel = document.getElementById('wheel');

            wheel.classList.add('animate-spin');

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

                    wheel.classList.remove('animate-spin');

                    this.congratulations();
                }
            }, 100);
        }
    }">
        <div id="confetti-container"></div>
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

        <div class="mx-auto text-center mt-24">
            <div id="wheel" class="wheel-container relative inline-block w-96">
                <svg viewBox="0 0 100 100" class="wheel bg-gray-800" style="border: 2px solid white; border-radius: 50%; width: 80%; height: 80%; margin: auto; transform: rotate(243.734deg); transition: none;">
<!--                    <path d="M50,50 L50,0 A50,50 0 0,1 100,50 Z" fill="rgb(27, 47, 73)" stroke="white" stroke-width="0.5"></path>-->
<!--                    <text x="74.74873734152916" y="25.251262658470836" text-anchor="middle" alignment-baseline="middle" fill="white"-->
<!--                          font-size="3" transform="rotate(90, 74.74873734152916, 25.251262658470836)">aaa</text>-->
<!--                    <path d="M50,50 L100,50 A50,50 0 0,1 50.00000000000001,100 Z" fill="rgb(27, 41, 76)" stroke="white"-->
<!--                          stroke-width="0.5"></path><text x="74.74873734152916" y="74.74873734152916" text-anchor="middle"-->
<!--                                                          alignment-baseline="middle" fill="white" font-size="3"-->
<!--                                                          transform="rotate(90, 74.74873734152916, 74.74873734152916)">bbb</text>-->
<!--                    <path d="M50,50 L50.00000000000001,100 A50,50 0 0,1 0,50.00000000000001 Z" fill="rgb(31, 42, 78)" stroke="white"-->
<!--                          stroke-width="0.5"></path><text x="25.25126265847084" y="74.74873734152916" text-anchor="middle"-->
<!--                                                          alignment-baseline="middle" fill="white" font-size="3"-->
<!--                                                          transform="rotate(90, 25.25126265847084, 74.74873734152916)">ccc</text>-->
<!--                    <path d="M50,50 L0,50.00000000000001 A50,50 0 0,1 49.999999999999986,0 Z" fill="rgb(31, 64, 68)" stroke="white"-->
<!--                          stroke-width="0.5"></path><text x="25.251262658470832" y="25.251262658470843" text-anchor="middle"-->
<!--                                                          alignment-baseline="middle" fill="white" font-size="3"-->
<!--                                                          transform="rotate(90, 25.251262658470832, 25.251262658470843)">ddd</text>-->
                </svg>
                <div
                        style="position: absolute; top: 0px; left: 50%; transform: translateX(-50%); width: 0px; height: 20px; border-top: 30px solid white; border-left: 20px solid transparent; border-right: 20px solid transparent; z-index: 2;">
                </div>
            </div>
        </div>

        <div class="max-w-xl m-auto bg-white rounded pt-12 pb-16 mt-6 overflow-hidden">
            <ul id="name-container" class="w-full max-h-2">
                <template x-for="item in items">
                    <li class="text-3xl text-center font-bold mb-8">
                        <span x-text="item"></span>
                    </li>
                </template>
            </ul>
        </div>

        <div class="text-center mt-12">
            <button x-on:click="start" class="mx-auto uppercase bg-gray-800 text-white px-4 py-2 rounded font-bold hover:bg-gray-700">Start</button>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
