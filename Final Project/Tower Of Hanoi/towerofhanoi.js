
let selectedDisk = null;

    function setupGame() {
      const numDisks = parseInt(document.getElementById("numDisks").value);
      const pegA = document.getElementById("pegA");
      const pegB = document.getElementById("pegB");
      const pegC = document.getElementById("pegC");

      [pegA, pegB, pegC].forEach(peg => peg.innerHTML = '<div class="rod"></div>');

      for (let i = numDisks; i >= 1; i--) {
        const disk = document.createElement("div");
        disk.classList.add("disk");
        disk.draggable = true;
        disk.style.width = `${60 + i * 20}px`;
        disk.style.background = `hsl(${i * 40}, 80%, 50%)`;
        disk.textContent = i;
        disk.dataset.size = i;
        pegA.appendChild(disk);
      }

      document.querySelectorAll(".disk").forEach(disk => {
        disk.addEventListener("dragstart", dragStart);
        disk.addEventListener("dragend", dragEnd);
      });

      document.querySelectorAll(".peg").forEach(peg => {
        peg.addEventListener("dragover", dragOver);
        peg.addEventListener("drop", dropDisk);
      });
    }

    function dragStart(e) {
      const peg = e.target.parentElement;
      const topDisk = peg.lastElementChild;
      if (e.target !== topDisk) {
        e.preventDefault();
        return;
      }
      selectedDisk = e.target;
      e.target.classList.add("dragging");
    }

    function dragEnd(e) {
      e.target.classList.remove("dragging");
      selectedDisk = null;
    }

    function dragOver(e) {
      e.preventDefault();
      this.classList.add("highlight");
    }

    function dropDisk(e) {
      e.preventDefault();
      this.classList.remove("highlight");

      if (!selectedDisk) return;

      const topDisk = this.lastElementChild;
      if (topDisk && topDisk.classList.contains("disk")) {
        const topSize = parseInt(topDisk.dataset.size);
        const selectedSize = parseInt(selectedDisk.dataset.size);
        if (selectedSize > topSize) return; // invalid move
      }

      this.appendChild(selectedDisk);

      checkWin();
    }

    function checkWin() {
      const pegC = document.getElementById("pegC");
      const numDisks = parseInt(document.getElementById("numDisks").value);
      const disksOnC = pegC.querySelectorAll(".disk").length;
      if (disksOnC === numDisks) {
        setTimeout(() => alert("🎉 You solved the Tower of Hanoi!"), 200);
      }
    }

    setupGame();
