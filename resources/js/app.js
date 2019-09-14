/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

M.AutoInit();

const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
    v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
    )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

// do the work...
document.querySelectorAll('.sortable th').forEach(th => th.addEventListener('click', (() => {
    var isDesc = th.classList.contains('sort-desc');
    const becomesDesc = !isDesc;
    const table = th.closest('table');
    Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
        .sort(comparer(Array.from(th.parentNode.children).indexOf(th), !becomesDesc))
        .forEach(tr => table.appendChild(tr) );
    
    Array.from(table.querySelectorAll('th')).forEach(function(el) {
      el.classList.remove("sort-asc");
      el.classList.remove("sort-desc");
      el.classList.remove("sort-header");
    });
    th.classList.add("sort-header");
    if (becomesDesc) {
      th.classList.add("sort-desc");
    } else {
      th.classList.add("sort-asc");
    }
})));

document.querySelectorAll('.sortable th.points-col').forEach(th => th.click());