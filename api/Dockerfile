FROM python:3.12-slim

# System deps: libmagic for python-magic, golang for flog
RUN apt-get update && \
    apt-get install -y --no-install-recommends libmagic1 golang-go git && \
    rm -rf /var/lib/apt/lists/*

# Install flog
ENV GOPATH=/go
ENV PATH="${GOPATH}/bin:${PATH}"
RUN go install github.com/mingrammer/flog@latest

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir --break-system-packages -r requirements.txt

COPY . .

EXPOSE 5000

CMD ["gunicorn", "--bind", "0.0.0.0:5000", "app:app"]
