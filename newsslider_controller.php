<?php

require_once("../../config.php");

function get_nsid_by_cmid($cmid) {
    if (!$cm = get_coursemodule_from_id('newsslider', $cmid)) {
        print_error('invalidcoursemodule');
    }

    return $cm->instance;
}

function get_rsslink_by_nsid($nsid) {
    global $DB;
    if (!$ns = $DB->get_record('newsslider', array('id' => $nsid))) {
        print_error('invalidcoursemodule');
    }

    return $ns->rss_link;
}

function get_newsslider_html($link = 'https://marsu.ru/rss/')
{
    $xml = file_get_contents($link);
    $parsed_xml = new SimpleXMLElement($xml);
    $news_xml = $parsed_xml->channel->item;
    $news = [];

    $news_num = count($news_xml) - 8; //12

    $months = ['янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];

    for ($i = 0; $i < $news_num; $i++) {
        $news[$i]['title'] = $news_xml[$i]->title;
        $news[$i]['description'] = $news_xml[$i]->description;
        $news[$i]['link'] = $news_xml[$i]->link;
        $news[$i]['image_link'] = $news_xml[$i]->enclosure->attributes()->url;
        $date_arr = date_parse($news_xml[$i]->pubDate);
        $date = $date_arr['day'] . ' ' . $months[$date_arr['month'] - 1] . ' ' . $date_arr['year'];
        $news[$i]['date'] = $date;
    }

    $pageHTML = '';

    $pageHTML .= '<div class="news-slider-wrapper">';
    $pageHTML .= '<div class="news-slider">';
    $pageHTML .= '
        <style type="text/css">
            :root {
                --slide__item-bg-color: #fff;
                --slide__item-max-w: 224px;
                --slide__item-max-h: 310px;
                --slide__item-between-margin: 15px;
                --slide__item-num: 4;
            }

            .news-slider-wrapper {
                display: flex;
                justify-content: center;
            }

            .news-slider {
                max-width: calc(var(--slide__item-max-w) * var(--slide__item-num) + var(--slide__item-between-margin) * (var(--slide__item-num) - 1));
            }

            .news-slider, .news-slider * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
                text-decoration: none;
            }

            .news-slider__container {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
            }

            .news__item {
                position: relative;
                display: none;
                flex-wrap: wrap;
                justify-content: center;
                width: var(--slide__item-max-w);
                height: var(--slide__item-max-h);
                background-color: var(--slide__item-bg-color);
            }

            .news__images-wrapper {
                position: absolute;
                top: 0;
                display: flex;
                align-items: flex-start;
                width: 100%;
                overflow: hidden;
                height: calc(var(--slide__item-max-h) / 1.6);
            }

            .news__image {
                width: 100%;
            }

            .news__date {
                position: absolute;
                bottom: 0;
                display: flex;
                align-items: center;
                padding: 0 15px;
                width: 100%;
                font-size: 14px;
                color: #999;
                background-color: #fff;
                background-clip: padding-box;
                height: calc(var(--slide__item-max-h) / 9);
                font-family: "Glober regular", sans-serif;
            }

            .news__item .news__content p.news__date {
                margin: 0;
            }

            .news__item .news__content p.news__title {
                margin: 0;
                line-height: normal;
            }

            .news__title {
                display: flex;
                width: 100%;
                padding: 0 15px;
                font-size: 14px;
                color: #4f4f4f;
                font-family: "Glober regular", sans-serif;

            }

            .news__content {
                position: absolute;
                bottom: 0;
                display: flex;
                flex-wrap: wrap;
                width: 100%;
                padding: 15px 0 0;
                background-color: #fff;
                height: calc(var(--slide__item-max-h) / 2.4);
                overflow: hidden;
                transition: height .25s ease-in-out;;
                outline: none;
            }

            .news__content:hover,
            .news__content:active,
            .news__content:focus {
                height: calc(var(--slide__item-max-h) / 1.5);
            }

            /*.news__link:hover + .news__content,
            .news__link:active .news__content,
            .news__link:focus .news__content{
                height: calc(var(--slide__item-max-h) / 1.5);
            }*/

            .news__link:link,
            .news__link:visited {
                color: #000;
                outline: none;
            }

            .news-slider__item {
                display: none;
                border: 1px solid #d7d7d7;
                margin-bottom: var(--slide__item-between-margin);
                margin-right: var(--slide__item-between-margin);
                opacity: 0;

                animation: fadeOut 1.25s;
                animation-fill-mode: forwards;
            }

            @keyframes fadeOut {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            .news-slider__item-visible {
                display: flex;
            }

            .news-slider__controls {
                display: flex;
                justify-content: center;
            }

            .news-slider__controls .controls__button {
                background-color: #fff;
                border: 1px solid #d7d7d7;
                color: #4f4f4f;
                font-size: 20px;
                padding: 5px 20px;
                cursor: pointer;
                transition: background-color 0.15s ease-in-out;
                outline: none;
            }

            .news-slider__controls .controls__button:hover,
            .news-slider__controls .controls__button:active,
            .news-slider__controls .controls__button:focus {
                background-color: #d7d7d7;
            }

            .controls__button--arrow-left {
                margin-right: var(--slide__item-between-margin);
            }
        </style>
    ';

    $pageHTML .= '<div class="news-slider__container news">';

    foreach ($news as $item) {
        $pageHTML .= '
            <section class="news__item news-slider__item">
                <div class="news__images-wrapper">
                    <img class="news__image" src="' . $item['image_link'] . '"
                         alt="Новость" loading="lazy" decoding="async">
                </div>
                <div class="news__content">
                    <a class="news__link" href="' . $item['link'] . '"><p class="news__title">' . $item['title'] . '</p></a>
                    <p class="news__date">' . $item['date'] . '</p>
                </div>
            </section>';

    }

    $pageHTML .= '</div>';

    $pageHTML .= '
        <div class="news-slider__controls controls">
            <button id="controls__button--arrow-left" class="controls__button controls__button--arrow-left" type="button"><</button>
            <button id="controls__button--arrow-right" class="controls__button controls__button--arrow-right" type="button">></button>
        </div>
    ';

    $pageHTML .= '
        <script type="text/javascript">
            // Возращает ширину элемента
            function getElementWidth(domElement) {
                return domElement.clientWidth;
            }

            function getCurVisible(curElementWidth, breakPoints, maxVisible) {
                let curVisible = 0;

                let breakPointIndex = breakPoints.findIndex(breakPoint => curElementWidth <= breakPoint);

                if (breakPointIndex !== - 1) {
                    if (breakPointIndex === 0) {
                        curVisible = 1;
                    } else {
                        curVisible = breakPointIndex + 1;
                    }
                } else {
                    curVisible = maxVisible;
                }

                return curVisible;
            }

            function getNumGroupsVisibleElements(numElements, curVisible) {
                let numGroupsVisibleElements = ~~(numElements / curVisible);

                if (numElements % curVisible !== 0)
                    numGroupsVisibleElements++;

                return numGroupsVisibleElements;
            }

            function getVisibleElements(curGroupVisibleElements, curVisible,
                                        numElements) {
                let toElement = curGroupVisibleElements * curVisible - 1;
                let fromElement = toElement - curVisible + 1;
                if (toElement > (numElements - 1)) toElement = numElements - 1;
                return [fromElement, toElement];
            }

            function setVisibleClass(domElements, visibleElements,
                                     visibleClass = "visible") {
                let fromElement = visibleElements[0];
                let toElement = visibleElements[1];

                domElements.forEach((el) => {
                    el.classList.remove(visibleClass);
                });

                for (let i = fromElement; i <= toElement; i++) {
                    domElements[i].classList.add(visibleClass);
                }
            }

            function changeGroupVisibleElements(curGroupVisibleElements, numGroupsVisibleElements,
                                                direction = "right") {
                if (direction === "left") {
                    if (curGroupVisibleElements === 1)
                        curGroupVisibleElements = numGroupsVisibleElements;
                    else if (curGroupVisibleElements > 1)
                        curGroupVisibleElements--;
                }

                if (direction === "right") {
                    if (curGroupVisibleElements === numGroupsVisibleElements)
                        curGroupVisibleElements = 1;
                    else if (curGroupVisibleElements < numGroupsVisibleElements)
                        curGroupVisibleElements++;
                }

                return curGroupVisibleElements;
            }

            function setCSSSlidesNum(curVisible, slideNumProp = "--slide__item-num") {
                document.documentElement.style.setProperty(slideNumProp, curVisible + "");
            }

            function removeLastSlideMargin(domElements, visibleElements, slideBetweenMargin) {
                domElements.forEach((el) => {
                    el.style.setProperty("margin-right", slideBetweenMargin + "px");
                });
                domElements[visibleElements[1]].style.setProperty("margin-right", 0 + "");
            }

            function updateSliderCSS(domElements, visibleElements, curVisible, slideBetweenMargin,
                                     slideNumProp = "--slide__item-num") {
                setCSSSlidesNum(curVisible, slideNumProp);
                removeLastSlideMargin(domElements, visibleElements, slideBetweenMargin);
            }

            function getSlideDomWidth(slideDomWidthProp = "--slide__item-max-w") {
                const docStyles = getComputedStyle(document.documentElement);
                return parseInt(docStyles.getPropertyValue(slideDomWidthProp));
            }

            function getSlideBetweenMargin(slideBetweenMarginProp = "--slide__item-between-margin") {
                const docStyles = getComputedStyle(document.documentElement);
                return parseInt(docStyles.getPropertyValue(slideBetweenMarginProp));
            }

            function getBreakPoints(slideDomWidth, slideBetweenMargin, maxVisible) {
                let breakPoints = [];
                for (let i = 0; i < maxVisible - 1; i++) {
                    if (!i)
                        breakPoints[i] = slideDomWidth * 2 + slideBetweenMargin * 2;
                    else
                        breakPoints[i] = breakPoints[i - 1] + slideDomWidth + slideBetweenMargin;
                }

                return breakPoints;
            }

            function checkCurVisibleChange(curVisible, prevVisible) {
                return curVisible !== prevVisible;
            }

            function resetCurGroupVisibleElements(reset = false, defValue) {
                if (reset) curGroupVisibleElements = defValue;
                return curGroupVisibleElements;
            }
            
            // Фикс для school.marsu.ru
            $(".mod-indent-outer .news-slider").parents(".mod-indent-outer").css("display", "block");    
            $(".mod-indent-outer .news-slider").parents(".contentafterlink").css("margin-left", 0);     

            // Список новостей (DOM элементы)
            let newsDom = document.querySelectorAll(".news__item");

            let newsDomWidth = getSlideDomWidth();
            let newsBetweenMargin = getSlideBetweenMargin();

            let slider_wrapper = document.querySelector(".news-slider-wrapper");

            let numElements = newsDom.length;
            let maxVisible = 4;
            let autoFlip = true;
            let flipInterval = 11250;

            // Ширины при которых происходит изменение кол-ва элементов в слайдере (px)
            let breakPoints = getBreakPoints(newsDomWidth, newsBetweenMargin, maxVisible);

            let prevVisible = maxVisible;
            let curVisible = getCurVisible(getElementWidth(slider_wrapper), breakPoints, maxVisible);
            let numGroupsVisibleElements = getNumGroupsVisibleElements(numElements, curVisible);
            let defCurGroupVisibleElements = 1;
            let curGroupVisibleElements = defCurGroupVisibleElements;
            let visibleElements = getVisibleElements(curGroupVisibleElements, curVisible, numElements);

            let visibleClass = "news-slider__item-visible";
            setVisibleClass(newsDom, visibleElements, visibleClass);
            updateSliderCSS(newsDom, visibleElements, curVisible);

            function checkWrapperSize() {
                new ResizeSensor(slider_wrapper, function() {
                    prevVisible = curVisible;
                    curVisible = getCurVisible(getElementWidth(slider_wrapper), breakPoints, maxVisible);
                    curGroupVisibleElements = resetCurGroupVisibleElements(
                        checkCurVisibleChange(curVisible, prevVisible),
                        defCurGroupVisibleElements
                    );
                    numGroupsVisibleElements = getNumGroupsVisibleElements(numElements, curVisible);
                    visibleElements = getVisibleElements(curGroupVisibleElements, curVisible, numElements);
                    setVisibleClass(newsDom, visibleElements, visibleClass);
                    updateSliderCSS(newsDom, visibleElements, curVisible, newsBetweenMargin);
                });
            }

            checkWrapperSize();

            let leftArrow = document.querySelector(".controls__button--arrow-left");
            let rightArrow = document.querySelector(".controls__button--arrow-right");

            let flipSlider = (e, direction) => {
                curGroupVisibleElements = changeGroupVisibleElements(curGroupVisibleElements, numGroupsVisibleElements, direction);
                visibleElements = getVisibleElements(curGroupVisibleElements, curVisible, numElements);
                curVisible = getCurVisible(getElementWidth(slider_wrapper), breakPoints, maxVisible, visibleElements);
                setVisibleClass(newsDom, visibleElements, visibleClass);
                updateSliderCSS(newsDom, visibleElements, curVisible, newsBetweenMargin);
            }

            if (autoFlip)
                setInterval(flipSlider.bind(this, "", "right"), flipInterval);

            leftArrow.addEventListener("click", flipSlider.bind(this, "", "left"), false);

            rightArrow.addEventListener("click", flipSlider.bind(this, "", "right"), false);
        </script>
    ';

    $pageHTML .= '</div>';
    $pageHTML .= '</div>';

    return $pageHTML;
}

if (isset($_GET['func'])) {
    if ($_GET['func'] === 'get_newsslider_html') {
        if (isset($_GET['cmid'])) {
            $nsid = get_nsid_by_cmid($_GET['cmid']);
            $link = get_rsslink_by_nsid($nsid);
            echo get_newsslider_html($link);
        }
    }
}

// Случай 1: вставка кода в описание при добавлении модуля на страницу курса
// Код ниже нужно поместить в описание модуля "newsslider", при его добавлении на страницу курса.
// Необходимо заменить "moodle369" на актуальную папку с мудл, если отсуствует переедресация в корень папки с мудл, иначе убрать из url.
/*<span id="newsslider"></span>
<script>
    $.get('/moodle369/mod/newsslider/newsslider_controller.php/?func=get_newsslider_html', (response) => {
        $('#newsslider').append(response);
    })
</script>*/

// Случай 2: вставка кода в описание посредством функции newsslider_add_instance
// Код из случая 1 присваивается свойству $newsslider->intro