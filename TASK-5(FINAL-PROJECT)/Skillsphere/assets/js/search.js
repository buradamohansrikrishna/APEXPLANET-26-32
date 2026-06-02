/* =========================================
   SKILLSPHERE SEARCH JS
   assets/js/search.js
========================================= */

// =========================================
// LIVE COURSE SEARCH
// =========================================

const searchInput =
document.getElementById(
    'searchInput'
);

const resultsContainer =
document.getElementById(
    'searchResults'
);

// =========================================
// SEARCH FUNCTION
// =========================================

async function searchCourses(){

    // CHECK INPUT

    if(!searchInput){

        return;

    }

    const query =
    searchInput.value.trim();

    // EMPTY INPUT

    if(query.length < 1){

        if(resultsContainer){

            resultsContainer.innerHTML = '';
            resultsContainer.hidden = true;

        }

        return;

    }

    if(resultsContainer){
        resultsContainer.hidden = false;
    }

    try{

        // FETCH AJAX SEARCH

        const response =
        await fetch(

            `ajax/search.php?query=${query}`

        );

        const data =
        await response.json();

        // CLEAR RESULTS

        resultsContainer.innerHTML = '';

        // SUCCESS

        if(data.status === 'success'){

            data.courses.forEach((course)=>{

                const courseCard =

                `
                <a
                    href="${course.url}"
                    class="search-card"
                >

                    <div class="search-image">

                        <img
                            src="uploads/thumbnails/${course.thumbnail}"
                            alt="${course.title}"
                        >

                    </div>

                    <div class="search-content">

                        <h3>

                            ${course.title}

                        </h3>

                        <p>

                            ${course.category}

                        </p>

                        <div class="search-meta">

                            <span>

                                👨‍🏫 ${course.instructor}

                            </span>

                            <span>

                                ⭐ ${course.level}

                            </span>

                        </div>

                        <h4>

                            ₹${course.price}

                        </h4>

                    </div>

                </a>
                `;

                resultsContainer.innerHTML +=
                courseCard;

            });

        }

        // EMPTY RESULTS

        else{

            resultsContainer.innerHTML =

            `
            <div class="search-empty">

                No Courses Found

            </div>
            `;

        }

    }

    catch(error){

        console.error(

            'Search Error:',
            error

        );

    }

}

// =========================================
// LIVE SEARCH EVENT WITH DEBOUNCING
// =========================================

let debounceTimeout;

function debouncedSearch(){
    if (!searchInput) return;
    const query = searchInput.value.trim();
    if(query.length < 1){
        clearTimeout(debounceTimeout);
        if(resultsContainer){
            resultsContainer.innerHTML = '';
            resultsContainer.hidden = true;
        }
        return;
    }
    showSearchLoader();
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(searchCourses, 300);
}

if(searchInput){

    searchInput.addEventListener(
        'keyup',
        debouncedSearch
    );

}

// =========================================
// SEARCH RESULT CLOSE
// =========================================

document.addEventListener(
    'click',
    (e)=>{

        if(

            resultsContainer &&
            searchInput &&
            !searchInput.contains(e.target) &&
            !resultsContainer.contains(e.target)

        ){

            resultsContainer.innerHTML = '';
            resultsContainer.hidden = true;

        }

    }
);

// =========================================
// SEARCH LOADER
// =========================================

function showSearchLoader(){

    if(resultsContainer){
        resultsContainer.hidden = false;
        resultsContainer.innerHTML =

        `
        <div class="search-skeleton-wrapper">
            <div class="search-skeleton-item">
                <div class="skeleton-shimmer skeleton-thumb"></div>
                <div class="skeleton-text-group">
                    <div class="skeleton-shimmer skeleton-title"></div>
                    <div class="skeleton-shimmer skeleton-meta"></div>
                </div>
            </div>
            <div class="search-skeleton-item">
                <div class="skeleton-shimmer skeleton-thumb"></div>
                <div class="skeleton-text-group">
                    <div class="skeleton-shimmer skeleton-title"></div>
                    <div class="skeleton-shimmer skeleton-meta"></div>
                </div>
            </div>
        </div>
        `;

    }

}

// =========================================
// SEARCH INPUT FOCUS EFFECT
// =========================================

if(searchInput){

    searchInput.addEventListener(
        'focus',
        ()=>{

            searchInput.classList.add(
                'search-active'
            );

        }
    );

    searchInput.addEventListener(
        'blur',
        ()=>{

            searchInput.classList.remove(
                'search-active'
            );

        }
    );

}

// =========================================
// CONSOLE MESSAGE
// =========================================

console.log(
    'SkillSphere Search System Loaded'
);