from cachetools import LRUCache

class Cache:
    table = LRUCache(maxsize=5)
    
    @staticmethod
    def set(key, val):
        Cache.table[key] = val

    @staticmethod
    def get(key):
        return Cache.table[key]
