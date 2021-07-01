const width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
const height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

let boardWidth = 0, boardHeight = 0;

function setup() {
    let board = "";

    for (let r = 0; r < Math.floor(height / 15); r++) {
        board += "<tr id=\"row" + r + "\">";
        boardHeight = r + 1;

        for (let c = 0; c < Math.floor(width / 15); c++) {
            board += "<td id=\"cell_" + r + "_" + c + "\" class=\"" + (Math.random() < 0.2 ? "alive" : "dead") + "\"></td>";
            boardWidth = c + 1;
        }

        board += "</tr>";
    }

    document.getElementById("board").innerHTML = board;
}

function update() {
    let newboard = [];

    for (let r = 0; r < boardHeight; r++) {
        newboard.push([]);

        for (let c = 0; c < boardWidth; c++) {
            newboard[r].push(0);
            newboard[r][c] += aliveDead(getCell(r + 1, c)) ? 1 : 0;
            newboard[r][c] += aliveDead(getCell(r + 1, c + 1)) ? 1 : 0;
            newboard[r][c] += aliveDead(getCell(r, c + 1)) ? 1 : 0;
            newboard[r][c] += aliveDead(getCell(r - 1, c + 1)) ? 1 : 0;
            newboard[r][c] += aliveDead(getCell(r - 1, c)) ? 1 : 0;
            newboard[r][c] += aliveDead(getCell(r - 1, c - 1)) ? 1 : 0;
            newboard[r][c] += aliveDead(getCell(r, c - 1)) ? 1 : 0;
            newboard[r][c] += aliveDead(getCell(r + 1, c - 1)) ? 1 : 0;

            if (aliveDead(getCell(r, c))) {
                newboard[r][c] = (newboard[r][c] > 1 && newboard[r][c] < 4);
            } else {
                newboard[r][c] = (newboard[r][c] === 3);
            }
        }
    }

    for (let r = 0; r < boardHeight; r++)
        for (let c = 0; c < boardWidth; c++) {
            if (newboard[r][c]) {
                getCell(r, c).className = "alive";
            } else {
                getCell(r, c).className = "dead";
            }
        }
}

function aliveDead(cell) {
    return cell.className === "alive";
}

function cellmod(i, b) {
    return (i + b) % b;
}

function getCell(r, c) {
    return document.getElementById("cell_" + cellmod(r, boardHeight) + "_" + cellmod(c, boardWidth));
}

function stopFunc() {
    clearInterval(conway);
}

function startFunc() {
    conway = setInterval(update, 500);
}