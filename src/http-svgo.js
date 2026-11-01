const {optimize} = require('svgo');
const http = require('http');

const host = '127.0.0.1';
const port = process.env.NODE_PORT || 8800;
const maxBodyBytes = 5 * 1024 * 1024;

const server = http.createServer(async (req, res) => {
	if (req.method !== 'POST') {
		res.writeHead(405, {'Allow': 'POST'});
		res.end('Method not allowed');
		return;
	}

	const contentLength = Number(req.headers['content-length'] || 0);
	if (!Number.isFinite(contentLength) || contentLength < 0 || contentLength > maxBodyBytes) {
		res.writeHead(413);
		res.end('Payload too large');
		return;
	}

	try {
		const buffers = [];
		let receivedBytes = 0;

		for await (const chunk of req) {
			receivedBytes += chunk.length;
			if (receivedBytes > maxBodyBytes) {
				res.writeHead(413);
				res.end('Payload too large');
				req.destroy();
				return;
			}
			buffers.push(chunk);
		}

		const svgString = Buffer.concat(buffers).toString();
		const result = optimize(svgString, {
			multipass: true,
		});

		res.writeHead(200, {'Content-Type': 'image/svg+xml; charset=utf-8'});
		res.end(result.data);
	} catch (error) {
		if (!res.headersSent) {
			res.writeHead(422);
		}
		res.end('Invalid SVG');
	}
});

server.requestTimeout = 10000;
server.headersTimeout = 5000;
server.keepAliveTimeout = 5000;

server.listen(port, host, () => {
	console.log(`HTTP SVGO Server is running on http://${host}:${port}`);
});
