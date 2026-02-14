const gridElement = document.getElementById('grid');
const nextElement = document.getElementById('next-preview');
const scoreElement = document.getElementById('score-val');
const speedSelect = document.getElementById('speed-select');

const WIDTH = 10;
const HEIGHT = 15;
let score = 0;
let gameGrid = Array(HEIGHT).fill().map(() => Array(WIDTH).fill(null));
let gameSpeed = 1; 
let gameTimeout = null;

const SHAPES = [
    { name: 'I', color: '#0ea5e9', shape: [[0,0], [0,1], [0,2], [0,3]] },
    { name: 'J', color: '#4f46e5', shape: [[0,0], [1,0], [1,1], [1,2]] },
    { name: 'L', color: '#f59e0b', shape: [[0,2], [1,0], [1,1], [1,2]] },
    { name: 'O', color: '#eab308', shape: [[0,0], [0,1], [1,0], [1,1]] },
    { name: 'S', color: '#22c55e', shape: [[0,1], [0,2], [1,0], [1,1]] },
    { name: 'T', color: '#a855f7', shape: [[0,1], [1,0], [1,1], [1,2]] },
    { name: 'Z', color: '#ef4444', shape: [[0,0], [0,1], [1,1], [1,2]] },
    { name: 'Plus', color: '#ec4899', shape: [[0,1], [1,0], [1,1], [1,2], [2,1]] },
    { name: 'SmallL', color: '#14b8a6', shape: [[0,0], [1,0], [1,1]] }
];

let currentPiece = null;
let nextPiece = null;

function initBoards() {
    gridElement.innerHTML = '';
    for (let i = 0; i < WIDTH * HEIGHT; i++) {
        const cell = document.createElement('div');
        cell.classList.add('cell');
        gridElement.appendChild(cell);
    }
    nextElement.innerHTML = '';
    for (let i = 0; i < 16; i++) {
        const cell = document.createElement('div');
        cell.classList.add('cell');
        nextElement.appendChild(cell);
    }
}

function getRandomPiece() {
    const type = SHAPES[Math.floor(Math.random() * SHAPES.length)];
    return {
        pos: { r: 0, c: 3 },
        shape: [...type.shape.map(c => [...c])],
        color: type.color
    };
}

function spawnPiece() {
    if (!nextPiece) nextPiece = getRandomPiece();
    currentPiece = nextPiece;
    nextPiece = getRandomPiece();
    
    if (checkCollision(currentPiece.pos.r, currentPiece.pos.c, currentPiece.shape)) {
        alert("Game Over! Score: " + score);
        gameGrid = Array(HEIGHT).fill().map(() => Array(WIDTH).fill(null));
        score = 0;
        scoreElement.innerText = score;
    }
    drawNext();
}

function drawNext() {
    const cells = nextElement.querySelectorAll('.cell');
    cells.forEach(c => c.style.backgroundColor = '');
    nextPiece.shape.forEach(([r, c]) => {
        const index = (r + 1) * 4 + (c + 1); // Offset to center in 4x4
        if(cells[index]) cells[index].style.backgroundColor = nextPiece.color;
    });
}

function draw() {
    const cells = gridElement.querySelectorAll('.cell');
    cells.forEach((cell, i) => {
        const r = Math.floor(i / WIDTH);
        const c = i % WIDTH;
        cell.style.backgroundColor = gameGrid[r][c] || '';
    });

    currentPiece.shape.forEach(([dr, dc]) => {
        const r = currentPiece.pos.r + dr;
        const c = currentPiece.pos.c + dc;
        if (r >= 0 && r < HEIGHT && c >= 0 && c < WIDTH) {
            cells[r * WIDTH + c].style.backgroundColor = currentPiece.color;
        }
    });
}

function checkCollision(r, c, shape) {
    return shape.some(([dr, dc]) => {
        const nr = r + dr;
        const nc = c + dc;
        return (nr >= HEIGHT || nc < 0 || nc >= WIDTH || (nr >= 0 && gameGrid[nr][nc]));
    });
}

function rotate() {
    const newShape = currentPiece.shape.map(([r, c]) => [c, -r]);
    const minR = Math.min(...newShape.map(p => p[0]));
    const minC = Math.min(...newShape.map(p => p[1]));
    const normalized = newShape.map(([r, c]) => [r - minR, c - minC]);

    if (!checkCollision(currentPiece.pos.r, currentPiece.pos.c, normalized)) {
        currentPiece.shape = normalized;
    }
    draw();
}

function moveDown() {
    if (!checkCollision(currentPiece.pos.r + 1, currentPiece.pos.c, currentPiece.shape)) {
        currentPiece.pos.r++;
    } else {
        currentPiece.shape.forEach(([dr, dc]) => {
            const r = currentPiece.pos.r + dr;
            const c = currentPiece.pos.c + dc;
            if (r >= 0) gameGrid[r][c] = currentPiece.color;
        });
        checkLines();
        spawnPiece();
    }
    draw();
}

function checkLines() {
    for (let r = HEIGHT - 1; r >= 0; r--) {
        if (gameGrid[r].every(v => v !== null)) {
            gameGrid.splice(r, 1);
            gameGrid.unshift(Array(WIDTH).fill(null));
            score += 100;
            scoreElement.innerText = score;
            r++;
        }
    }
}

// Game Loop with variable speed
function gameLoop() {
    moveDown();
    const baseInterval = 800;
    // Speed formula: Interval / Factor. (e.g., 2x speed means 400ms interval)
    const currentInterval = baseInterval / parseFloat(speedSelect.value);
    
    clearTimeout(gameTimeout);
    gameTimeout = setTimeout(gameLoop, currentInterval);
}

// Controls
window.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft' && !checkCollision(currentPiece.pos.r, currentPiece.pos.c - 1, currentPiece.shape)) currentPiece.pos.c--;
    if (e.key === 'ArrowRight' && !checkCollision(currentPiece.pos.r, currentPiece.pos.c + 1, currentPiece.shape)) currentPiece.pos.c++;
    if (e.key === 'ArrowDown') moveDown();
    if (e.key === 'ArrowUp' || e.key === ' ') rotate();
    draw();
});

document.getElementById('btn-left').onclick = () => { if(!checkCollision(currentPiece.pos.r, currentPiece.pos.c - 1, currentPiece.shape)) currentPiece.pos.c--; draw(); };
document.getElementById('btn-right').onclick = () => { if(!checkCollision(currentPiece.pos.r, currentPiece.pos.c + 1, currentPiece.shape)) currentPiece.pos.c++; draw(); };
document.getElementById('btn-rotate').onclick = rotate;
document.getElementById('btn-down').onclick = moveDown;

initBoards();
spawnPiece();
gameLoop();
