/**
 * Created by Misha on 06.06.2017.
 */
// Вешаем обработчик на нажатия кнопок постраничной навигации
$('#news-wrapper').on('click', '#news-pagination a', function() {
	var href = $(this).attr('href'); // Определяем ссылку
	// Пустые ссылки не обрабатываем
	if (href != '') {
		// Для индикации работы через ajax делаем элемент-обёртку полупрозрачным
		var wrapper = $('#news-wrapper');
		wrapper.css('opacity', .5);
		// Запрашиваем страницу через ajax
		$.get(href, function(res) {
			// При получении любого ответа делаем обёртку обратно непрозрачной
			wrapper.css('opacity', 1);
			// Получен успешный ответ
			if (res.success) {
				// Меняем содержимое элементов
				// новости
				$('#news-items').html(res.data['items']);
				// постраничная навигация
				$('#news-pagination').html(res.data['pagination']);
			}
			// Ответ с ошибкой и в массиве данных указан адрес перенаправления
			else if (res.data['redirect']) {
				// Редиректим пользователя
				window.location = res.data['redirect'];
			}
			// Иначе пишем ошибку в консоль и больше ничего не делаем
			else {
				console.log(res);
				// А вообще, здесь можно и вывести ошибку на экран
				// alert(res.data['message']);
			}
		}, 'json');
	}
	// В любом случае не даём перейти по ссылке - у нас же тут ajax пагинация
	return false;
});