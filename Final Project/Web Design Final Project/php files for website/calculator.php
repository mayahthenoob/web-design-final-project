<?php
require_once 'utils.php'; 
renderModal(); 
renderHead('Calculator');
renderHeader('calculator.php', $authUser);
?>

<style>
    /* Calculator Styles (match your working one exactly) */
    #calculator {
        border: 4px solid #f59e0b;
        border-radius: 12px;
        padding: 20px;
        background: #eee;
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        max-width: 300px;
        width: 100%;
    }

    #display {
        width: 100%;
        height: 60px;
        border: 2px solid #333;
        border-radius: 8px;
        font-size: 2em;
        text-align: right;
        padding: 10px;
        background: white;
        color: #111;
        margin-bottom: 20px;
    }

    .keypad {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    .keypad button {
        padding: 15px;
        font-size: 1.2em;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }

    /* Number buttons */
    .num {
        background: #fff;
        color: #333;
    }

    /* Operator buttons */
    .op {
        background: #d97706;
        color: white;
    }

    /* Equals button */
    .equals {
        background: #10b981;
        color: white;
        grid-column: span 2;
    }

    /* Modal */
    .modal {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 2000;
    }
    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 8px;
        text-align: center;
        max-width: 300px;
    }
    .modal-content button {
        background-color: #f59e0b;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        margin-top: 15px;
        cursor: pointer;
    }
</style>

<main class="container" style="padding: 40px 20px; display:flex; justify-content:center;">
    <section style="text-align:center;">

        <h1 style="margin-bottom:20px;">Calculator</h1>

        <div id="calculator">
            <input type="text" id="display" readonly>

            <div class="keypad">

                <button class="op" onclick="clearMe()">C</button>
                <button class="op" onclick="switchSigns()">+/-</button>
                <button class="op" onclick="percentMe()">%</button>
                <button class="op" onclick="displayMe('/')">/</button>

                <button class="num" onclick="displayMe('7')">7</button>
                <button class="num" onclick="displayMe('8')">8</button>
                <button class="num" onclick="displayMe('9')">9</button>
                <button class="op" onclick="displayMe('*')">*</button>

                <button class="num" onclick="displayMe('4')">4</button>
                <button class="num" onclick="displayMe('5')">5</button>
                <button class="num" onclick="displayMe('6')">6</button>
                <button class="op" onclick="displayMe('-')">-</button>

                <button class="num" onclick="displayMe('1')">1</button>
                <button class="num" onclick="displayMe('2')">2</button>
                <button class="num" onclick="displayMe('3')">3</button>
                <button class="op" onclick="displayMe('+')">+</button>

                <button class="num" onclick="displayMe('0')">0</button>
                <button class="num" onclick="displayMe('.')">.</button>
                <button class="equals" onclick="calculate()">=</button>

            </div>
        </div>

        <div style="margin-top:20px;">
            <a href="buy-now.php" style="color:#f59e0b; font-weight:600;">Back to Order Options</a>
        </div>

    </section>
</main>

<!-- Modal -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <p id="modalMessage"></p>
        <button onclick="closeModal()">OK</button>
    </div>
</div>

<script>
function showModal(msg) {
    document.getElementById('modalMessage').innerText = msg;
    document.getElementById('messageModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('messageModal').style.display = 'none';
}

function displayMe(val) {
    document.getElementById('display').value += val;
}

function calculate() {
    const display = document.getElementById('display');
    try {
        display.value = Function("return " + display.value)();
    } catch {
        showModal("Invalid expression");
        display.value = "";
    }
}

function clearMe() {
    document.getElementById('display').value = "";
}

function switchSigns() {
    const d = document.getElementById('display');
    if (d.value && !isNaN(parseFloat(d.value))) {
        d.value = parseFloat(d.value) * -1;
    }
}

function percentMe() {
    const d = document.getElementById('display');
    if (d.value && !isNaN(parseFloat(d.value))) {
        d.value = parseFloat(d.value) / 100;
    }
}
</script>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>

</body>
</html>
