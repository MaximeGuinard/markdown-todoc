document.getElementById('convert-button').addEventListener('click', function() {
    // Récupère le texte Markdown de l'input
    const markdownText = document.getElementById('markdown-input').value;

    // Conversion simple du Markdown en HTML (exemple basique)
    const htmlText = markdownToHTML(markdownText);
    
    // Affiche le résultat dans la section Google Docs
    document.getElementById('result').innerHTML = htmlText;
});

// Fonction de conversion Markdown en HTML simple
function markdownToHTML(markdown) {
    // Remplace les **bold** par <strong>
    markdown = markdown.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    // Remplace les *italic* par <em>
    markdown = markdown.replace(/\*(.*?)\*/g, '<em>$1</em>');
    // Remplace les # Titre par <h1>, ## Titre par <h2>, etc.
    markdown = markdown.replace(/^(#{1,6})\s*(.+)$/gm, (match, hash, title) => {
        const level = hash.length;
        return `<h${level}>${title}</h${level}>`;
    });
    // Remplace les retours à la ligne par des <br>
    markdown = markdown.replace(/\n/g, '<br>');
    
    return markdown;
}
