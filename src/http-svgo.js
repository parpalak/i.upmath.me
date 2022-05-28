const {optimize} = require('svgo');
const http = require('http');

const host = 'localhost';
const port = process.env.NODE_PORT || 8800;

const server = http.createServer(async (req, res) => {
	const buffers = [];

	for await (const chunk of req) {
		buffers.push(chunk);
	}

	const svgString = Buffer.concat(buffers).toString();

	const result = optimize(svgString, {
		multipass: true,
	});

	res.end(result.data);
});

server.listen(port, host, () => {
	console.log(`HTTP SVGO Server is running on http://${host}:${port}`);
});
