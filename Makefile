init:
	npm install ./frontend
	composer install --working-dir=./backend
frontend:
	npm start --prefix frontend
backend:
	symfony --dir=./backend server:start