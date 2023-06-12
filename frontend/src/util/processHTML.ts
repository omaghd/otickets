const processHTML = (html: string) =>
  html.replace(/(^([ ]*<p><br><\/p>)*)|((<p><br><\/p>)*[ ]*$)/gi, '').trim()

export default processHTML
