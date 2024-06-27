<?php

require __DIR__.'/../bootstrap.php';

$items = json_decode(
    file_get_contents(__DIR__ . '/../items.txt')
);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($items)) {
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
        intervalId: null,
        items: [
            <?php foreach ($items as $item): ?>
                '<?php echo $item; ?>',
            <?php endforeach; ?>
        ],
        init() {
            //
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
        <div class="max-w-5xl m-auto mt-64 bg-white rounded pt-12 pb-16 overflow-hidden">
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
