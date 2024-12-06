// タスクリストをAjaxで非同期に操作
    document.addEventListener('DOMContentLoaded', function () {
        // フォームの送信をキャッチして、Ajaxで送信する
        const form = document.querySelector('form');
        form.addEventListener('submit', function (e) {
            e.preventDefault();  // ページのリロードを防ぐ

            const taskInput = document.querySelector('input[name="txt"]');
            const taskText = taskInput.value.trim();

            if (taskText === '') {
                alert('タスクを入力してください');
                return;
            }

            const formData = new FormData(form);  // フォームデータを取得

            // タスクを非同期で追加
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.text();
                } else {
                    throw new Error('タスクの追加に失敗しました');
                }
            })
            .then(() => {
                taskInput.value = '';  // 入力フォームをクリア
                location.reload();     // ページをリロードしてリストを更新
            })
            .catch(error => {
                console.error(error);
            });
        });

        // 削除ボタンをキャッチして、Ajaxで削除する
        const deleteButtons = document.querySelectorAll('input[name="del"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();  // ページのリロードを防ぐ

                const formData = new FormData();
                formData.append('del', this.value);  // 削除するIDを追加

                // タスクを非同期で削除
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    } else {
                        throw new Error('タスクの削除に失敗しました');
                    }
                })
                .then(() => {
                    location.reload();  // ページをリロードしてリストを更新
                })
                .catch(error => {
                    console.error(error);
                });
            });
        });
    });