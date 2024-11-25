const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    mode: 'development', // або 'production'
    entry: {
        'main': './scss/main.scss', // Загальний вхідний файл
        'blocks/hero': './blocks/hero/hero.scss', // SCSS для hero блоку
        'blocks/about': './blocks/about/about.scss', // SCSS для hero блоку
    },
    output: {
        path: path.resolve(__dirname, 'dist'), // Вихідна папка
        filename: '[name].bundle.js', // Вихідний JS
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader, // Витягує CSS у файл
                    'css-loader', // Завантажує CSS
                    'sass-loader', // Компілює SCSS у CSS
                ],
            },
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name].css',
        }),
    ],
};
